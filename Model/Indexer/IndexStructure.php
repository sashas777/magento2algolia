<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2023  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\AlgoliaConfigSearch\Model\Indexer;

use Algolia\AlgoliaSearch\Helper\AlgoliaHelper;
use Magento\Framework\Indexer\IndexStructureInterface;
use Psr\Log\LoggerInterface;

/**
 * Class IndexStructure
 */
class IndexStructure implements IndexStructureInterface
{
    /**
     * @var AlgoliaHelper
     */
    protected $algoliaHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param AlgoliaHelper $algoliaHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        AlgoliaHelper $algoliaHelper,
        LoggerInterface $logger
    ) {
        $this->algoliaHelper = $algoliaHelper;
        $this->logger = $logger;
    }

    /**
     * @param $index
     * @param array $dimensions
     *
     * @return void
     */
    public function delete($index, array $dimensions = [])
    {
        $this->algoliaHelper->deleteIndex($index);
        $this->logger->debug('Algolia: index deleted: '.$index);
    }

    /**
     * @param $index
     * @param array $fields
     * @param array $dimensions
     *
     * @return void
     */
    public function create($index, array $fields, array $dimensions = [])
    {
        $this->logger->critical('Algolia::IndexStructure::create');
        // Algolia does not require to create an index
    }
}
