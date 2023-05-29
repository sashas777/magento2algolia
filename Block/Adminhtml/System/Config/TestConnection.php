<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2023  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */
declare(strict_types=1);

namespace TheSGroup\AlgoliaConfigSearch\Block\Adminhtml\System\Config;

/**
 * Algolia test connection block
 */
class TestConnection extends \Magento\AdvancedSearch\Block\Adminhtml\System\Config\TestConnection
{
    /**
     * @inheritdoc
     */
    protected function _getFieldMapping(): array
    {
        $fields = [

        ];

        return array_merge(parent::_getFieldMapping(), $fields);
    }
}
