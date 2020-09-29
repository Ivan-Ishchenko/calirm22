<?php

namespace Ids\Configurabletabletier\Plugin\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;

class ConfigurablePrice {

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @param LocatorInterface $locator
     */
    public function __construct(
        LocatorInterface $locator
    ) {
        $this->locator = $locator;
    }

    public function afterModifyMeta(
        \Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\ConfigurablePrice $subject,
        $meta
    ) {


        $product = $this->locator->getProduct();

        if ($product) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $helper = $objectManager->get(\Ids\Configurabletabletier\Helper\Data::class);
            /* @var $helper \Ids\Configurabletabletier\Helper\Data */
            $attributeSets = $helper->geAttributeSetsFromConfig();
            $productAttributeSetId = $product->getAttributeSetId();

            if ($helper->isModuleActive() && in_array($productAttributeSetId, $attributeSets)) {
                //TODO Use $productTypeId = $this->locator->getProduct()->getTypeId();



                if (isset($meta['product-details']['children']['container_price']['children']['advanced_pricing_button']['arguments']['data']['config']['visible'])) {
                    $meta['product-details']['children']['container_price']['children']['advanced_pricing_button']['arguments']['data']['config']['visible'] = 1;
                }

                if (isset($meta['product-details']['children']['container_price']['children']['advanced_pricing_button']['arguments']['data']['config']['disabled'])) {
                    $meta['product-details']['children']['container_price']['children']['advanced_pricing_button']['arguments']['data']['config']['disabled'] = 0;
                }

                if (isset($meta['advanced_pricing_modal']['children']['advanced-pricing']['children']['container_special_price']['children']['special_price']['arguments']['data']['config'])) {
                    $meta['advanced_pricing_modal']['children']['advanced-pricing']['children']['container_special_price']['children']['special_price']['arguments']['data']['config']['disabled'] = 1;
                }

                if (isset($meta['advanced_pricing_modal']['children']['advanced-pricing']['children']['container_special_from_date']['children']['special_from_date']['arguments']['data']['config'])) {
                    $meta['advanced_pricing_modal']['children']['advanced-pricing']['children']['container_special_from_date']['children']['special_from_date']['arguments']['data']['config']['disabled'] = 1;
                }

                if (isset($meta['advanced_pricing_modal']['children']['advanced-pricing']['children']['container_special_from_date']['children']['special_to_date']['arguments']['data']['config'])) {
                    $meta['advanced_pricing_modal']['children']['advanced-pricing']['children']['container_special_from_date']['children']['special_to_date']['arguments']['data']['config']['disabled'] = 1;
                }

            }
        }

        return $meta;
    }

}