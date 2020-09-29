<?php

namespace Ids\Configurabletabletier\Model\Observer;

class SalesQuoteRemoveItem implements \Magento\Framework\Event\ObserverInterface {




    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlInterface;

    /**
     * [__construct ]
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\UrlInterface           $urlInterface
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->_objectManager = $objectManager;
        $this->_urlInterface = $urlInterface;
    }





    /*private $_sessionManager;

    public function __construct(
        \Magento\Framework\Session\SessionManager $sessionManager
    ) {
        $this->_sessionManager = $sessionManager;
    }*/

    public function execute(\Magento\Framework\Event\Observer $observer) {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
            if ($ip !== '185.43.236.108') {
            return true;
        }






        $quoteItem = $observer->getQuoteItem();
        if ($quoteItem) {
            /**
             * @var $quote \Magento\Quote\Model\Quote
             */
            $quote = $quoteItem->getQuote();
            if($quote) {
                //$quote->collectTotals()->save();

                //$quote->setTotalsCollectedFlag(false)->collectTotals()->save();

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();




                /**  TODO Delete
                 * @var \Magento\Framework\App\Request\Http $request */
                $request = $objectManager->get(\Magento\Framework\App\Request\Http::class);

/*
                //$cartData = $request->getParam('cart');
                $cartData = $request->getParams();

        echo '<pre>';
        var_dump($cartData);
        echo '</pre>';
        exit;*/


                /**
                 * @var $cart \Magento\Checkout\Model\Cart
                 */
                $cart = $objectManager->get(\Magento\Checkout\Model\Cart::class);
                if($cart) {
                    /*$cart->getQuote()->setTotalsCollectedFlag(false);
                    $cart->save();*/
                    //$cart->getQuote()->setTotalsCollectedFlag(false)->collectTotals()->save();
                    //$cart->truncate()->save();
                    //$cart->saveQuote();

                    // get array of all items what can be display directly
                    $itemsVisible = $cart->getQuote()->getAllVisibleItems();
                    if(count($itemsVisible) > 0) {
                        $cartData = array();

                        foreach ($itemsVisible as $itemVisible) {
                            $itemId = $itemVisible['item_id'];
                            $itemQty = $itemVisible['qty'];

                            $cartData[$itemId] = array('qty' => $itemQty);
                        }

                        if(count($cartData) > 0) {
//                            $cartData = $cart->suggestItemsQty($cartData);
//                            $cart->updateItems($cartData)->save();




                            /** @var \Magento\Framework\Data\Form\FormKey $formKey */
                            $formKey = $objectManager->get(\Magento\Framework\Data\Form\FormKey::class);
                            $postData = [
                                'cart' => $cartData,
                                'update_cart_action' => 'update_qty',
                                'form_key' => $formKey->getFormKey(),
                            ];
                            //$request->setPostValue($postData);

                            /** Execute SUT */
                            //$this->dispatch('checkout/cart/updatePost');

                            $uri = 'checkout/cart/updatePost';

                            //$request->setRequestUri($uri);




//                            $url = $this->_urlInterface->getUrl($uri); // give here your controller/action
//                            // below code redirects to cart controller
//                            $request->getControllerAction()
//                                ->getResponse()
//                                ->setPostValue($postData)
//                                ->setRedirect($url);






                            //$this->_getBootstrap()->runApp();

                            //$cart->updateItems($cartData);
                            //$cart->saveQuote();



                            /**
                             * @var $eventManager \Magento\Framework\Event\ManagerInterface
                             */
                            /*$eventManager = $objectManager->get(\Magento\Framework\Event\ManagerInterface::class);

                            $infoDataObject = new \Magento\Framework\DataObject($cartData);

                            $eventManager->dispatch(
                                'checkout_cart_update_items_after',
                                ['cart' => $this, 'info' => $infoDataObject]
                            );*/
                        }
                    }


                }
            }
        }


        /*echo '<pre>';
        print_r('SalesQuoteRemoveItem');
        echo '</pre>';
        exit;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //@var $cart \Magento\Checkout\Model\Cart
        //$cart = $objectManager->get(\Magento\Checkout\Model\Cart::class);

        $request = $objectManager->get('Magento\Framework\App\Action\Context')->getRequest();
        $itemId = (int)$request->getParam('id');

        //$quoteItem = $observer->getQuoteItem();
        //if ($quoteItem && $itemId > 0) {
        if ($itemId > 0) {

            // @var $quote \Magento\Quote\Model\Quote
            //$quote = $quoteItem->getQuote();

            //if($quote) {



                //$cart->save();
            //}
        }*/
    }



    /**
     * Bootstrap instance getter
     *
     * @return \Magento\TestFramework\Helper\Bootstrap
     */
    protected function _getBootstrap()
    {
        return \Magento\TestFramework\Helper\Bootstrap::getInstance();
    }


    public function execute222(\Magento\Framework\Event\Observer $observer) {

        $quoteItem = $observer->getQuoteItem();
        if ($quoteItem) {

            /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $request = $objectManager->get('Magento\Framework\App\Action\Context')->getRequest();

            $cartData = $request->getParam('cart');

            echo '<pre>';
            print_r($request->getParams());
            echo '</pre>';


            echo '<pre>';
            print_r($cartData);
            echo '</pre>';
            exit;*/


            /**
             * @var $quote \Magento\Quote\Model\Quote
             */
            $quote = $quoteItem->getQuote();

            if($quote) {




                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                /**
                 * @var $cart \Magento\Checkout\Model\Cart
                 */
                $cart = $objectManager->get(\Magento\Checkout\Model\Cart::class);

                /**
                 * @var $checkoutSession \Magento\Checkout\Model\Session
                 */
                $checkoutSession = $objectManager->get(\Magento\Checkout\Model\Session::class);

                if ($items = $cart->getQuote()->getAllVisibleItems()) {
                    $cartData = array();
                    $filter = new \Zend_Filter_LocalizedToNormalized(
                        ['locale' => $objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                    );
                    foreach ($items as $item) {
                        /* @var $item \Magento\Quote\Model\Quote\Item */



                        //if($item->isDeleted() === false) {
                            $cartData[$item->getId()]['qty'] = $filter->filter(trim($item->getQty()));
                        //}

                    }

                    if(count($cartData) > 0) {
                        //$cartData = $cart->suggestItemsQty($cartData);
                        $cart->updateItems($cartData);
                        $cart->save();

                        $checkoutSession->setCartWasUpdated(true);

                        /*echo '<pre>';
                        print_r($cartData);
                        echo '</pre>';
                        exit;*/

                    }
                }

                //$quote->afterSave();

                //$cart->save();
                //$cart->saveQuote();
                //$checkoutSession->setCartWasUpdated(true);


                /*$cartData = $this->getRequest()->getParam('cart');
                if (is_array($cartData)) {
                    $filter = new \Zend_Filter_LocalizedToNormalized(
                        ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                    );
                    foreach ($cartData as $index => $data) {
                        if (isset($data['qty'])) {
                            $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                        }
                    }
                    if (!$this->cart->getCustomerSession()->getCustomerId() && $this->cart->getQuote()->getCustomerId()) {
                        $this->cart->getQuote()->setCustomerId(null);
                    }

                    $cartData = $this->cart->suggestItemsQty($cartData);
                    $this->cart->updateItems($cartData)->save();
                }*/
            }
        }
    }
}