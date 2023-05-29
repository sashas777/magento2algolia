<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2023  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\AlgoliaConfigSearch\Model\Indexer;

use Magento\Catalog\Model\Category;
use Magento\CatalogSearch\Model\Indexer\Fulltext\Processor;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Indexer\CacheContext;
use Magento\Framework\Indexer\SaveHandler\Batch;
use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use TheSGroup\AlgoliaConfigSearch\Model\Client\Algolia;
use TheSGroup\AlgoliaConfigSearch\SearchAdapter\Adapter;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Indexer\IndexStructureInterface;

/**
 * Class IndexerHandler
 */
class IndexerHandler implements IndexerInterface
{
    /**
     * Size of default batch
     */
    const DEFAULT_BATCH_SIZE = 1;

    /**
     * @var Algolia
     */
    protected $algoliaClient;

    /**
     * @var int
     */
    protected $batchSize;

    /**
     * @var Batch
     */
    protected $batch;

    /**
     * @var ScopeResolverInterface
     */
    protected $scopeResolver;

    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * @var IndexStructureInterface
     */
    protected $indexStructure;

    /**
     * @var CacheContext|mixed
     */
    protected $cacheContext;

    /**
     * @var Processor|mixed
     */
    protected $processor;

    /**
     * @param Algolia $algoliaClient
     * @param Batch $batch
     * @param ScopeResolverInterface $scopeResolver
     * @param Adapter $adapter
     * @param IndexStructureInterface $indexStructure
     * @param CacheContext|null $cacheContext
     * @param Processor|null $processor
     */
    public function __construct(
        Algolia $algoliaClient,
        Batch $batch,
        ScopeResolverInterface $scopeResolver,
        Adapter $adapter,
        IndexStructureInterface $indexStructure,
        ?CacheContext $cacheContext = null,
        ?Processor $processor = null
    ) {
        $this->algoliaClient = $algoliaClient;
        $this->batch = $batch;
        $this->scopeResolver = $scopeResolver;
        $this->adapter = $adapter;
        $this->indexStructure = $indexStructure;
        $this->batchSize = static::DEFAULT_BATCH_SIZE;
        $this->cacheContext = $cacheContext ?: ObjectManager::getInstance()->get(CacheContext::class);
        $this->processor = $processor ?: ObjectManager::getInstance()->get(Processor::class);
    }

    /**
     * @param $dimensions
     * @param \Traversable $documents
     *
     * @return $this|IndexerInterface
     * @throws \Exception
     */
    public function saveIndex($dimensions, \Traversable $documents)
    {
        $dimension = current($dimensions);
        $scopeId = (string) $this->scopeResolver->getScope($dimension->getValue())->getId();
        $indexName = $this->algoliaClient->getIndexName($scopeId);

        foreach ($this->batch->getItems($documents, $this->batchSize) as $documentsBatch) {

            $docs = $this->adapter->prepareDocsPerStore($documentsBatch, $scopeId);
            //@todo move out
            $indexSettings = [
                'attributesForFaceting' => [
                    'category_ids',
                    'visibility'
                ]
            ];

//            var_dump($docs);
            $this->algoliaClient->saveIndex($indexName, $docs, $indexSettings);

            if ($this->processor->getIndexer()->isScheduled()) {
                $this->updateCacheContext($docs);
            }
        }

        return $this;
    }

    /**
     * @param $dimensions
     * @param \Traversable $documents
     *
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteIndex($dimensions, \Traversable $documents)
    {
        $dimension = current($dimensions);
        $scopeId = (string) $this->scopeResolver->getScope($dimension->getValue())->getId();
        $this->indexStructure->delete($this->algoliaClient->getIndexName($scopeId), $dimensions);
    }

    /**
     * @param $dimensions
     *
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function cleanIndex($dimensions)
    {
        $dimension = current($dimensions);
        $scopeId = (string) $this->scopeResolver->getScope($dimension->getValue())->getId();
        $this->indexStructure->delete($this->algoliaClient->getIndexName($scopeId), $dimensions);
    }

    /**
     * @param $dimensions
     *
     * @return bool
     */
    public function isAvailable($dimensions = [])
    {
       return $this->algoliaClient->testConnection();
    }

    /**
     * Add category cache tags for the affected products to the cache context
     *
     * @param array $docs
     * @return void
     */
    private function updateCacheContext(array $docs) : void
    {
        $categoryIds = [];
        foreach ($docs as $document) {
            if (!empty($document['category_ids'])) {
                if (is_array($document['category_ids'])) {
                    foreach ($document['category_ids'] as $id) {
                        $categoryIds[] = $id;
                    }
                } elseif (is_numeric($document['category_ids'])) {
                    $categoryIds[] = $document['category_ids'];
                }
            }
        }
        if (!empty($categoryIds)) {
            $categoryIds = array_unique($categoryIds);
            $this->cacheContext->registerEntities(Category::CACHE_TAG, $categoryIds);
        }
    }
}
