<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2023  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\AlgoliaConfigSearch\SearchAdapter\Dynamic;


use Magento\Elasticsearch\SearchAdapter\QueryAwareInterface;
use Magento\Framework\Search\Dynamic\EntityStorage;
use Magento\Framework\Search\Dynamic\IntervalInterface;
use Magento\Framework\Search\Request\BucketInterface;

class DataProvider implements \Magento\Framework\Search\Dynamic\DataProviderInterface, QueryAwareInterface
{
    public function getRange()
    {
        // TODO: Implement getRange() method.
    }

    public function getAggregations(\Magento\Framework\Search\Dynamic\EntityStorage $entityStorage)
    {
        // TODO: Implement getAggregations() method.
    }

    public function getInterval(
        BucketInterface $bucket,
        array $dimensions,
        \Magento\Framework\Search\Dynamic\EntityStorage $entityStorage
    ) {
        // TODO: Implement getInterval() method.
    }

    public function getAggregation(
        BucketInterface $bucket,
        array $dimensions,
        $range,
        \Magento\Framework\Search\Dynamic\EntityStorage $entityStorage
    ) {
        // TODO: Implement getAggregation() method.
    }

    public function prepareData($range, array $dbRanges)
    {
        // TODO: Implement prepareData() method.
    }

}
