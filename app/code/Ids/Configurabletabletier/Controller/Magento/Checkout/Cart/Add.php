<?php

namespace Ids\Configurabletabletier\Controller\Magento\Checkout\Cart;


class Add extends \Magento\Checkout\Controller\Cart\Add {

    /**
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute() {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            //TODO not used $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                return $this->goBack();
            }

            $this->_checkoutSession->setIdsLastAddedProductId($product->getId());

            /* TODO Ids Commented
            $this->cart->addProduct($product, $params);
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }

            $this->cart->save();
            TODO END Ids Commented*/


            /* IDS Added */

            /* @var $helper \Ids\Configurabletabletier\Helper\Data */
            $helper = $this->_objectManager->get(\Ids\Configurabletabletier\Helper\Data::class);
            $attributeSets = $helper->geAttributeSetsFromConfig();
            $productAttributeSetId = $product->getAttributeSetId();

            if ($helper->isModuleActive() && \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE === $product->getTypeId() && in_array($productAttributeSetId, $attributeSets)) {
            //if ($helper->isModuleActive() && in_array($productAttributeSetId, $attributeSets)) {
                $isOneOfSubProductsAddedToCart = false;
                $products = $this->getRequest()->getParam('subproduct');
                unset($params['subproduct']);

                if(is_array($products)) {
                    foreach ($products as $key => $value) {
                        $storeId = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
                        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->setStoreId($storeId)->load($params['product']);

                        $attributes = $product->getTypeInstance(true)
                            ->getConfigurableAttributes($product);

                        $subproduct = $this->_objectManager->create(\Magento\Catalog\Model\Product::class)->load($key);

                        $params['qty'] = 0;

                        $params['selected_configurable_option'] = $key;

                        $params['super_attribute'] = array();
                        foreach ($attributes as $atr) {
                            $params['super_attribute'][$atr->getProductAttribute()->getId()] = $subproduct->getData($atr->getProductAttribute()->getAttributeCode());
                        }

                        /* TODO Delete */
                        /*$params['super_attribute']['options'] = array(
                            //'option_id' => 'option_value'
                            186 => 3705
                        );*/
                        /* TODO END Delete */


                        $params['qty'] = $products[$key]['qty'];

                        if ($params['qty'] > 0) {

                            $productId = $key;

                            $stockItem = $this->_objectManager->get('Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory')->create();
                            $stockItemResource = $this->_objectManager->get(\Magento\CatalogInventory\Model\ResourceModel\Stock\Item::class);
                            $storeManager = $this->_objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);

                            $stockItemResource->loadByProductId($stockItem, $productId, $storeManager->getWebsite()->getId());

                            $stockItemData = $stockItem->getData();

                            if (isset($stockItemData['qty']) && $params['qty'] > $stockItemData['qty']) {
                                //TODO __('Please correct the quantity for some products.')
                                //TODO __('The most you may purchase is %1.', $stockItem->getMaxSaleQty() * 1)
                                //$this->messageManager->addErrorMessage(__("We don't have some of the products you want."));
                                //TODO $this->messageManager->addErrorMessage(__("%1 is not enough", $subproduct->getSku()));
                                $this->messageManager->addErrorMessage(__('We don\'t have as many "%1" as you requested.', $subproduct->getSku()));
                                //throw new \Magento\Framework\Exception\LocalizedException(__("%1 is not enough", $subproduct->getSku()));
                                //TODO $this->_getSession()->addError($subproduct->getSku() . ' is not enough');
                            } else {
                                $this->cart->addProduct($product, $params);
                                $isOneOfSubProductsAddedToCart = true;
                            }
                        }

                    }
                }

                if ($isOneOfSubProductsAddedToCart) { /* IDS Added if */
                    $this->cart->save();

                    $this->_checkoutSession->setCartWasUpdated(true);


                    /**
                     * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
                     */
                    $this->_eventManager->dispatch(
                        'checkout_cart_add_product_complete',
                        ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
                    );

                    if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                        if (!$this->cart->getQuote()->getHasError()) {
                            $message = __(
                                'You added %1 to your shopping cart.',
                                $product->getName()
                            );
                            $this->messageManager->addSuccessMessage($message);
                        }
                        return $this->goBack(null, $product);
                    }
                } else {
                    $this->messageManager->addErrorMessage(__('Please correct the quantity for some products.'));
                } /* END IDS Added if */
            } else {
                return parent::execute();
            }
            /* END IDS Added */

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNotice(
                    $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addError(
                        $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($message)
                    );
                }
            }

            $url = $this->_checkoutSession->getRedirectUrl(true);

            if (!$url) {
                $cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
                $url = $this->_redirect->getRedirectUrl($cartUrl);
            }

            return $this->goBack($url);

        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return $this->goBack();
        }

        return $this->goBack();
    }

}
	
	