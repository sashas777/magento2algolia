<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2023  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\AlgoliaConfigSearch\SearchAdapter;

use Magento\Elasticsearch\Model\Adapter\BatchDataMapperInterface;
use Magento\Elasticsearch\SearchAdapter\AggregationFactory;
use Magento\Framework\Api\Search\DocumentFactory;
use Magento\Framework\Search\AdapterInterface;
use Magento\Framework\Search\Request\QueryInterface;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Response\QueryResponseFactory;
use Psr\Log\LoggerInterface;
use TheSGroup\AlgoliaConfigSearch\Model\Client\Algolia;
use Magento\Framework\App\ScopeResolverInterface;

class Adapter implements AdapterInterface
{
    protected $batchDocumentDataMapper;
    protected $responseFactory;
    protected $documentFactory;
    protected $aggregationFactory;

    protected $algolia;

    protected $scopeResolver;

    protected $mapper;

    protected $logger;

    public function __construct(
        BatchDataMapperInterface $batchDocumentDataMapper,
        QueryResponseFactory $responseFactory,
        DocumentFactory $documentFactory,
        AggregationFactory $aggregationFactory,
        Algolia $algolia,
        ScopeResolverInterface $scopeResolver,
        Mapper $mapper,
        LoggerInterface $logger
    ) {
        $this->batchDocumentDataMapper = $batchDocumentDataMapper;
        $this->responseFactory = $responseFactory;
        $this->documentFactory = $documentFactory;
        $this->aggregationFactory = $aggregationFactory;
        $this->algolia = $algolia;
        $this->scopeResolver = $scopeResolver;
        $this->mapper = $mapper;
        $this->logger = $logger;
    }

    public function query(RequestInterface $request)
    {
        $dimension = current($request->getDimensions());
        $storeId = (string) $this->scopeResolver->getScope($dimension->getValue())->getId();
        $indexName = $this->algolia->getIndexName($storeId);

        $this->logger->debug('Search Query Index: '.$indexName);

        $filters = $this->mapper->buildQuery($request);
        $this->logger->debug('Filters: '.json_encode($filters));

        $filters['sumOrFiltersScores'] = true;
        $filters['length'] = $request->getSize();

        $searchQuery = '';
        if (isset($filters['query'])) {
            $searchQuery = $filters['query'];
            $this->logger->debug('Query param: '.$searchQuery);
            unset($filters['query']);
        }

        //@todo implement and verify
//https://www.algolia.com/doc/api-reference/api-parameters/sumOrFiltersScores/
//        var_dump($request->getAggregation());

        $documents = [];
        $response = $this->algolia->query($indexName, $searchQuery, $filters);
       foreach ($response['hits'] as $rawDocument) {
           /** @var \Magento\Framework\Api\Search\Document[] $documents */
           $documents[] = $this->documentFactory->create(
               $rawDocument
           );
       }
        /** @var \Magento\Framework\Api\Search\Document[] $documents */
//        $documents = $this->documentFactory->create([]); //$rawDocument

        $response = ['total' => (int) $response['nbHits']];

        /** @var \Magento\Framework\Search\Response\Aggregation $aggregations */
        $aggregations = $this->aggregationFactory->create([]); //$response['aggregations']

        $queryResponse = $this->responseFactory->create(
            [
                'documents' => $documents,
                'aggregations' => $aggregations,
                'total' => $response['total']
            ]
        );

        return $queryResponse;
    }



    /**
     * Create Elasticsearch documents by specified data
     *
     * @param array $documentData
     * @param int $storeId
     * @return array
     */
    public function prepareDocsPerStore(array $documentData, $storeId)
    {
        $documents = [];
        if (count($documentData)) {
            $documents = $this->batchDocumentDataMapper->map(
                $documentData,
                $storeId
            );
        }
        //  Required for Algolia
        foreach ($documents as $id => $document) {
            $documents[$id]['objectID'] = $id;
        }
        return $documents;
    }

}
