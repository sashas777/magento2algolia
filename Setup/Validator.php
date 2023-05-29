<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2023  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\AlgoliaConfigSearch\Setup;

use Magento\Search\Model\SearchEngine\ValidatorInterface;
use Algolia\AlgoliaSearch\Helper\AlgoliaHelper;
use TheSGroup\AlgoliaConfigSearch\Model\Client\Algolia;

/**
 * Class Validator
 */
class Validator implements ValidatorInterface
{
    /**
     * @var Algolia
     */
    protected $algoliaClient;

    /**
     * @param Algolia $algoliaClient
     */
    public function __construct(
        Algolia $algoliaClient
    ) {
        $this->algoliaClient = $algoliaClient;
    }

    /**
     * @return array|string[]
     */
    public function validate(): array
    {
        $errors = [];
        try {
            if (!$this->algoliaClient->testConnection()) {
                $errors[] = 'Could not validate a connection to Algolia.'
                            . ' Verify that the Algolia configured correctly and has indexes.';
            }
        } catch (\Exception $e) {
            $errors[] = 'Could not validate a connection to Algolia. ' . $e->getMessage();
        }
        return $errors;
    }
}
