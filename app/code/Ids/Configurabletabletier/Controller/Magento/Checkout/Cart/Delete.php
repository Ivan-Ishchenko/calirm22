<?php

namespace Ids\Configurabletabletier\Controller\Magento\Checkout\Cart;


class Delete extends \Magento\Checkout\Controller\Cart\Delete {

    /**
     * Delete shopping cart item action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute() {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if ($ip !== '185.43.236.108') {
            return parent::execute();
        }


        return parent::execute();


        echo '<pre>';
        print_r($this->getRequest()->getParams());
        echo '</pre>';

        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $id = (int)$this->getRequest()->getParam('id');

        /* IDS */
        echo '<pre>';
        var_dump($id);
        echo '</pre>';
        exit;
        /* END IDS */

        if ($id) {
            try {

                /**
                 * @var $sidebar \Magento\Checkout\Model\Sidebar
                 */
                //$sidebar = $this->_objectManager->get(\Magento\Checkout\Model\Sidebar::class);
                //$sidebar->removeQuoteItem($id);

                //TODO $this->cart->removeItem($id)->save();
                $this->cart->removeItem($id);
                $this->cart->save();

            } catch (\Exception $e) {
                $this->messageManager->addError(__('We can\'t remove the item.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
        }
        $defaultUrl = $this->_objectManager->create('Magento\Framework\UrlInterface')->getUrl('*/*');
        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRedirectUrl($defaultUrl));
    }

}
	