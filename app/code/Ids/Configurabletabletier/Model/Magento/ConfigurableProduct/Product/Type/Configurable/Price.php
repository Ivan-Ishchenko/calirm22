<?php

namespace Ids\Configurabletabletier\Model\Magento\ConfigurableProduct\Product\Type\Configurable;


class Price extends \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Price {

    /**
     * Get product final price
     *
     * @param   float $qty
     * @param   \Magento\Catalog\Model\Product $product
     * @return  float
     */
    public function getFinalPrice($qty, $product) {
        $finalPrice = parent::getFinalPrice($qty, $product);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get(\Ids\Configurabletabletier\Helper\Data::class);
        /* @var $helper \Ids\Configurabletabletier\Helper\Data */
        $attributeSets = $helper->geAttributeSetsFromConfig();
        $productAttributeSetId = $product->getAttributeSetId();

        if ($helper->isModuleActive() && in_array($productAttributeSetId, $attributeSets)) {
            if ($this->getTierPriceCount($product) > 0) {

                $priceInfo = $product->getPriceInfo();
                $configurableProductBasePrice = $priceInfo->getPrice('final_price')->getAmount()->getValue();


                $configurableProductPrice = parent::getBasePrice($product, $qty);
                $configurableProductFinalPrice = $finalPrice;

                $deltaCustomOptions = ($configurableProductFinalPrice > $configurableProductPrice) ? $configurableProductFinalPrice - $configurableProductPrice : 0;


                $tierPrice = $this->calculateConfigurableProductTierPricing($product);


                if ($tierPrice > $configurableProductBasePrice) {
                    $tierPrice = $configurableProductBasePrice;
                }

                if ($tierPrice > 0 && $tierPrice < $finalPrice) {
                    $finalPrice = $tierPrice;
                }


                if (isset($deltaCustomOptions) && $deltaCustomOptions > 0) {
                    $finalPrice += $deltaCustomOptions;
                }


                $deltaAssociatedProduct = 0;
                if ($product->getCustomOption('simple_product') && $product->getCustomOption('simple_product')->getProduct()) {
                    $associatedProductPrice = parent::getFinalPrice($qty, $product->getCustomOption('simple_product')->getProduct());


                    if ($associatedProductPrice > 0 && $associatedProductPrice > $configurableProductBasePrice) {
                        $deltaAssociatedProduct = $associatedProductPrice - $configurableProductBasePrice;
                    }
                }

                if (isset($deltaAssociatedProduct) && $deltaAssociatedProduct > 0) {
                    $finalPrice += $deltaAssociatedProduct;
                }


            }
        }

        return $finalPrice;
    }


    /**
     * Count how many tier prices we have for the product
     *
     * @param   Product $product
     * @return  int
     */
    public function getTierPriceCount($product) {
        $price = $product->getTierPrice();
        return count($price);
    }


    /**
     * Calculate configurable product tier pricing
     *
     * @param $product
     * @return array|float|int
     */
    public function calculateConfigurableProductTierPricing($product) {
        $tierPrice = 0;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get(\Magento\Checkout\Model\Cart::class);
        //TODO $cart = $objectManager->get(\Magento\Checkout\Model\Session::class);

        if ($items = $cart->getQuote()->getItemsCollection()) {
            $id_quantities = array();
            foreach ($items as $item) {

                if ($item->getParentItem())
                    continue;

                $id = $item->getProductId();

                $id_quantities[$id][] = $item->getQty();
            }

            if (array_key_exists($product->getId(), $id_quantities)) {

                $total_qty = array_sum($id_quantities[$product->getId()]);
                $tierPrice = $this->getTierPrice($total_qty, $product);
            }
        }

        return $tierPrice;
    }

}
	
	