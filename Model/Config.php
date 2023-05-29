<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2023  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\AlgoliaConfigSearch\Model;


use Magento\AdvancedSearch\Model\Client\ClientOptionsInterface;

class Config implements ClientOptionsInterface
{
    public function prepareClientOptions($options = [])
    {
        // TODO: Implement prepareClientOptions() method.
    }

}
