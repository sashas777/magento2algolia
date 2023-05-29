<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2023  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\AlgoliaConfigSearch\Model\ResourceModel\Fulltext\Collection;

use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection\DefaultFilterStrategyApplyCheckerInterface;

/**
 * This class add in backward compatibility purposes to check if need to apply old strategy for filter prepare process.
 * @deprecated 100.3.2
 */
class DefaultFilterStrategyApplyChecker implements DefaultFilterStrategyApplyCheckerInterface
{
    /**
     * Check if this strategy applicable for current engine.
     *
     * @return bool
     */
    public function isApplicable(): bool
    {
        return false;
    }
}
