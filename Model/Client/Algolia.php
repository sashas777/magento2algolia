<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2023  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\AlgoliaConfigSearch\Model\Client;

use Algolia\AlgoliaSearch\Helper\AlgoliaHelper;
use Algolia\AlgoliaSearch\Helper\Data;
use Magento\AdvancedSearch\Model\Client\ClientInterface;
use Magento\CatalogSearch\Model\Indexer\Fulltext;
use Psr\Log\LoggerInterface;

class Algolia implements ClientInterface
{
    /**
     * Catalog FullTextSearch Prefix
     */
    const INDEX_SUFFIX = '_' . Fulltext::INDEXER_ID;

    /**
     * @var AlgoliaHelper
     */
    protected $algoliaHelper;

    protected $baseHelper;

    protected $logger;

    public function __construct(
        AlgoliaHelper $algoliaHelper,
        Data $baseHelper,
        LoggerInterface $logger
    ) {
        $this->algoliaHelper = $algoliaHelper;
        $this->baseHelper = $baseHelper;
        $this->logger = $logger;
    }

    public function testConnection(): bool
    {
        $indexes = $this->algoliaHelper->listIndexes();
        if (!count($indexes)) {
            return false;
        }
       return true;
    }

    public function query($indexName, $q, $params)
    {
        return $this->algoliaHelper->query($indexName, $q, $params);
    }

    public function saveIndex(string $indexName, array $docs, array $settings): void
    {
        try {
            $index = $this->algoliaHelper->getIndex($indexName);
            $index->setSettings($settings);
            $index->saveObjects($docs);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw $e;
        }
    }

    /**
     * Get Index Name for Algolia
     * @param string $storeId
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getIndexName(string $storeId)
    {
        return $this->baseHelper->getIndexName(static::INDEX_SUFFIX, $storeId);
    }
}
