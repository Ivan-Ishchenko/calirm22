<?php

namespace Ids\Configurabletabletier\Controller\Magento\Checkout\Cart;

class UpdateItemOptions extends \Magento\Checkout\Controller\Cart\UpdateItemOptions {

    /**
     * Update product configuration for a cart item
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute() {
        $id = (int)$this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();

        if (!isset($params['options'])) {
            $params['options'] = [];
        }
        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            /**
             * @var $quoteItem \Magento\Quote\Model\Quote\Item
             */
            $quoteItem = $this->cart->getQuote()->getItemById($id);
            if (!$quoteItem) {
                throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t find the quote item.'));
            }

            /* IDS Added */

            $productId = $quoteItem->getData('product_id');
            $storeId = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
            $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->setStoreId($storeId)->load($productId);

            $buyRequestData = $quoteItem->getBuyRequest()->getData();

            $associatedSimpleProductId = 0;
            if ($quoteItem->getProduct()->getCustomOption('simple_product') && $quoteItem->getProduct()->getCustomOption('simple_product')->getProduct()) {
                $associatedSimpleProductId = $quoteItem->getProduct()->getCustomOption('simple_product')->getProduct()->getId();
            }

            if($associatedSimpleProductId > 0) {
                /* @var $helper \Ids\Configurabletabletier\Helper\Data */
                $helper = $this->_objectManager->get(\Ids\Configurabletabletier\Helper\Data::class);
                $attributeSets = $helper->geAttributeSetsFromConfig();
                $productAttributeSetId = $product->getAttributeSetId();

                if ($helper->isModuleActive() && \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE === $product->getTypeId() && in_array($productAttributeSetId, $attributeSets)) {
                //TODO if ($helper->isModuleActive() && in_array($productAttributeSetId, $attributeSets)) {
                    $products = $this->getRequest()->getParam('subproduct');
                    unset($params['subproduct']);

                    if(is_array($products) && count($products) > 0) {
                        foreach ($products as $key => $value) {
                            $subProductId = $key;

                            if ($subProductId == $associatedSimpleProductId) {

                                $params['qty'] = $products[$key]['qty'];

                                $params['super_attribute'] = (isset($buyRequestData['super_attribute'])) ? $buyRequestData['super_attribute'] : array();

                                $item = $this->cart->updateItem($id, new \Magento\Framework\DataObject($params));
                                if (is_string($item)) {
                                    throw new \Magento\Framework\Exception\LocalizedException(__($item));
                                }
                                if ($item->getHasError()) {
                                    throw new \Magento\Framework\Exception\LocalizedException(__($item->getMessage()));
                                }

                                $related = $this->getRequest()->getParam('related_product');
                                if (!empty($related)) {
                                    $this->cart->addProductsByIds(explode(',', $related));
                                }

                                $this->cart->save();

                                $this->_eventManager->dispatch(
                                    'checkout_cart_update_item_complete',
                                    ['item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
                                );
                                if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                                    if (!$this->cart->getQuote()->getHasError()) {
                                        $message = __(
                                            '%1 was updated in your shopping cart.',
                                            $this->_objectManager->get('Magento\Framework\Escaper')
                                                ->escapeHtml($item->getProduct()->getName())
                                        );
                                        $this->messageManager->addSuccess($message);
                                    }
                                    return $this->_goBack($this->_url->getUrl('checkout/cart'));
                                }

                                break;
                            }
                        }
                    }
                }
            }
            /* END IDS Added */

            return parent::execute();

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addError($message);
                }
            }

            $url = $this->_checkoutSession->getRedirectUrl(true);
            if ($url) {
                return $this->resultRedirectFactory->create()->setUrl($url);
            } else {
                $cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
                return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRedirectUrl($cartUrl));
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t update the item right now.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return $this->_goBack();
        }
        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
