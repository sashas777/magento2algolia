<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2023  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\AlgoliaConfigSearch\SearchAdapter\Aggregation;


use Magento\Framework\Search\Dynamic\IntervalInterface;

class Interval implements IntervalInterface
{
    public function load($limit, $offset = null, $lower = null, $upper = null)
    {
        // TODO: Implement load() method.
    }

    public function loadPrevious($data, $index, $lower = null)
    {
        // TODO: Implement loadPrevious() method.
    }

    public function loadNext($data, $rightIndex, $upper = null)
    {
        // TODO: Implement loadNext() method.
    }

}
