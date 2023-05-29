<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2023  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\AlgoliaConfigSearch\Model\ResourceModel\Fulltext\Collection;

use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection\TotalRecordsResolverInterface;
use Magento\Framework\Api\Search\SearchResultInterface;

/**
 * Resolve total records count.
 */
class TotalRecordsResolver implements TotalRecordsResolverInterface
{
    /**
     * @var SearchResultInterface
     */
    private $searchResult;

    /**
     * @param SearchResultInterface $searchResult
     */
    public function __construct(
        SearchResultInterface $searchResult
    ) {
        $this->searchResult = $searchResult;
    }

    /**
     * @inheritdoc
     */
    public function resolve(): ?int
    {
        return $this->searchResult->getTotalCount();
    }
}
