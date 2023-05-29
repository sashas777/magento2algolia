<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2023  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\AlgoliaConfigSearch\SearchAdapter;

use Magento\Elasticsearch\SearchAdapter\Query\Builder\MatchQuery as MatchQueryBuilder;
use Magento\Framework\Search\Request\Query\BoolExpression;
use Magento\Framework\Search\Request\Query\Filter;
use Magento\Framework\Search\Request\Query\MatchQuery;
use Magento\Framework\Search\Request\QueryInterface;
use Magento\Framework\Search\RequestInterface;
use Psr\Log\LoggerInterface;

class Mapper
{
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Build adapter dependent query
     *
     * @param RequestInterface $request
     * @return array
     */
    public function buildQuery(RequestInterface $request): array
    {
        $this->logger->debug('Search Request Name: '.$request->getName());
        foreach ($request->getDimensions() as $dimension) {
            $this->logger->debug('Dimension: '.$dimension->getName().' '.$dimension->getValue());
        }
        $this->logger->debug('Search Query Type: '.$request->getQuery()->getType());
        $searchQuery = $this->processQuery(
            $request->getQuery(),
            [],
            BoolExpression::QUERY_CONDITION_MUST
        );

        $searchQuery['filters'] = implode(' AND ', $searchQuery['filters']);
        //$this->queryBuilder->initAggregations($request, $searchQuery);
        return $searchQuery;
    }

    /**
     * Process query
     *
     * @param QueryInterface $requestQuery
     * @param array $selectQuery
     * @param string $conditionType
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function processQuery(
        QueryInterface $requestQuery,
        array $selectQuery,
        string $conditionType
    ) {
        switch ($requestQuery->getType()) {
            case QueryInterface::TYPE_MATCH:
                /** @var MatchQuery $requestQuery */
                $this->logger->debug('Processing '.$requestQuery->getType().' Query.');
                $this->logger->debug('VALUE '. $requestQuery->getValue());
                $selectQuery['query'] = $requestQuery->getValue();
//                $selectQuery = $this->matchQueryBuilder->build(
//                    $selectQuery,
//                    $requestQuery,
//                    $conditionType
//                );
                break;
            case QueryInterface::TYPE_BOOL:
                /** @var BoolExpression $requestQuery */
                $this->logger->debug('Processing '.$requestQuery->getType().' Query.');
                $selectQuery = $this->processBoolQuery($requestQuery, $selectQuery);
                break;
            case QueryInterface::TYPE_FILTER:
                /** @var Filter $requestQuery */
                $this->logger->debug('Processing '.$requestQuery->getType().' Query.');
                $selectQuery = $this->processFilterQuery($requestQuery, $selectQuery, $conditionType);
                break;
            default:
                throw new \InvalidArgumentException(sprintf(
                    'Unknown query type \'%s\'',
                    $requestQuery->getType()
                ));
        }

        return $selectQuery;
    }

    /**
     * Process bool query
     *
     * @param BoolExpression $query
     * @param array $selectQuery
     * @return array
     * @since 100.2.2
     */
    protected function processBoolQuery(
        BoolExpression $query,
        array $selectQuery
    ) {
        $selectQuery = $this->processBoolQueryCondition(
            $query->getMust(),
            $selectQuery,
            BoolExpression::QUERY_CONDITION_MUST
        );

        $selectQuery = $this->processBoolQueryCondition(
            $query->getShould(),
            $selectQuery,
            BoolExpression::QUERY_CONDITION_SHOULD
        );

        $selectQuery = $this->processBoolQueryCondition(
            $query->getMustNot(),
            $selectQuery,
            BoolExpression::QUERY_CONDITION_NOT
        );

        return $selectQuery;
    }

    /**
     * Process bool query condition (must, should, must_not)
     *
     * @param QueryInterface[] $subQueryList
     * @param array $selectQuery
     * @param string $conditionType
     * @return array
     */
    protected function processBoolQueryCondition(
        array $subQueryList,
        array $selectQuery,
        $conditionType
    ) {
        foreach ($subQueryList as $subQuery) {
            $selectQuery = $this->processQuery($subQuery, $selectQuery, $conditionType);
        }

        return $selectQuery;
    }

    /**
     * Process filter query
     *
     * @param Filter $query
     * @param array $selectQuery
     * @param string $conditionType
     * @return array
     */
    private function processFilterQuery(
        Filter $query,
        array $selectQuery,
        $conditionType
    ) {
        switch ($query->getReferenceType()) {
            case Filter::REFERENCE_QUERY:
                $this->logger->debug('!!!Query not implemented');

//                $selectQuery = $this->processQuery($query->getReference(), $selectQuery, $conditionType);
                break;
            case Filter::REFERENCE_FILTER:
                $conditionType = $conditionType === BoolExpression::QUERY_CONDITION_NOT ?
                    MatchQueryBuilder::QUERY_CONDITION_MUST_NOT : $conditionType;

                /** @var \Magento\Framework\Search\Request\Filter\Term $reference */
                $reference = $query->getReference();
                $this->logger->debug('Filter '.$reference->getName().': '
                                     .$reference->getField().' '
                                     .json_encode($reference->getValue())
                );


                $value = $reference->getValue();
                if (is_string($value)) {
                    //@todo add escape
                    $filterValue = $reference->getField().':\''.$value.'\'';
                }elseif (is_array($value)) {

                    foreach ($value as $k=>$val) {
                        $value[$k] = $reference->getField().':\''.$val.'\'';
                    }

                    $filterValue = '('.implode(' OR ',$value).')';
                } else {
                    $this->logger->critical('Unsupported filter type: '.gettype($value));
                }

                if (!array_key_exists('filter', $selectQuery)) {
                    $selectQuery['filters'][] = $filterValue;
                } else {
                    $selectQuery['filters'][]= $filterValue;
                }

//                $selectQuery['filter'] = array_merge(
//                        $selectQuery['filter'] ?? [],
//                            [$filterValue]
//                    );
//                $filterQuery = $this->filterBuilder->build($query->getReference(), $conditionType);
//                foreach ($filterQuery['bool'] as $condition => $filter) {
//                    //phpcs:ignore Magento2.Performance.ForeachArrayMerge
//                    $selectQuery['bool'][$condition] = array_merge(
//                        $selectQuery['bool'][$condition] ?? [],
//                        $filter
//                    );
//                }
                break;
        }

        return $selectQuery;
    }
}
