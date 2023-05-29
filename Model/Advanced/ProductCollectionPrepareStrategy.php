<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2023  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\AlgoliaConfigSearch\Model\Advanced;


use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogSearch\Model\Advanced\ProductCollectionPrepareStrategyInterface;
use Magento\Framework\App\ObjectManager;

class ProductCollectionPrepareStrategy implements ProductCollectionPrepareStrategyInterface
{
    /**
     * @var Config
     */
    private $catalogConfig;

    /**
     * @var Visibility
     */
    private $catalogProductVisibility;

    /**
     * @param Config $catalogConfig
     * @param Visibility|null $catalogProductVisibility
     */
    public function __construct(
        Config $catalogConfig,
        Visibility $catalogProductVisibility = null
    ) {
        $this->catalogConfig = $catalogConfig;
        $this->catalogProductVisibility = $catalogProductVisibility
                                          ?? ObjectManager::getInstance()->get(Visibility::class);
    }

    /**
     * @inheritdoc
     */
    public function prepare(Collection $collection)
    {
        $collection
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addMinimalPrice()
            ->addTaxPercents()
            ->setVisibility($this->catalogProductVisibility->getVisibleInSearchIds());
    }
}
