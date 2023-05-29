<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2023  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\AlgoliaConfigSearch\Model\ResourceModel\Fulltext\Collection;

use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection\SearchCriteriaResolverInterface;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Data\Collection;

class SearchCriteriaResolver implements SearchCriteriaResolverInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $builder;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var string
     */
    private $searchRequestName;

    /**
     * @var int
     */
    private $size;

    /**
     * @var array
     */
    private $orders;

    /**
     * @var int
     */
    private $currentPage;

    /**
     * SearchCriteriaResolver constructor.
     * @param SearchCriteriaBuilder $builder
     * @param Collection $collection
     * @param string $searchRequestName
     * @param int $currentPage
     * @param int $size
     * @param array $orders
     */
    public function __construct(
        SearchCriteriaBuilder $builder,
        Collection $collection,
        string $searchRequestName,
        int $currentPage,
        int $size,
        ?array $orders
    ) {
        $this->builder = $builder;
        $this->collection = $collection;
        $this->searchRequestName = $searchRequestName;
        $this->currentPage = $currentPage;
        $this->size = $size;
        $this->orders = $orders;
    }

    /**
     * @inheritdoc
     */
    public function resolve(): SearchCriteria
    {
        $searchCriteria = $this->builder->create();
        $searchCriteria->setRequestName($this->searchRequestName);
        $searchCriteria->setSortOrders($this->orders);
        $searchCriteria->setCurrentPage($this->currentPage - 1);
        if ($this->size) {
            $searchCriteria->setPageSize($this->size);
        }

        return $searchCriteria;
    }
}
