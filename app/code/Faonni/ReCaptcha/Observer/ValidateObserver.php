<?php
/**
 * Copyright © 2011-2017 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\ReCaptcha\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Phrase;
use Faonni\ReCaptcha\Helper\Data as ReCaptchaHelper;
use Faonni\ReCaptcha\Model\Provider;

/**
 * ReCaptcha Validate observer
 */
class ValidateObserver implements ObserverInterface
{
    /**
     * Helper instance
     *
     * @var \Faonni\ReCaptcha\Helper\Data
     */
    protected $_helper; 
    
    /**
     * Provider instance
     * 
     * @var \Faonni\ReCaptcha\Model\Provider
     */
    protected $_provider;	     
    
	/**
     * Response Redirect instance
     * 	
	 * @var \Magento\Framework\App\Response\RedirectInterface
	 */
	protected $_redirect;

	/**
     * ActionFlag instance
     * 		
	 * @var \Magento\Framework\App\ActionFlag
	 */
	protected $_actionFlag; 
	
    /**
     * Message Manager instance
     * 	
     * @var \Magento\Message\ManagerInterface
     */
    protected $_messageManager;	       
        
    /**
     * Initialize observer
     * 
     * @param Data $helper
     * @param Context $context
     * @param Provider $provider
     */
    public function __construct(
        ReCaptchaHelper $helper,
		Context $context,
		Provider $provider         
    ) {
        $this->_helper = $helper;
		$this->_redirect = $context->getRedirect();
		$this->_actionFlag = $context->getActionFlag();
		$this->_messageManager = $context->getMessageManager();
		$this->_provider = $provider;
    }

    /**
     * Handler for controller action predispatch event
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
		$request = $observer->getEvent()->getRequest();	
		$action = strtolower($request->getFullActionName());
		
		if ($request->isPost() && $this->_helper->isPostAllowed($action)) {
			$recaptcha = $request->getPost('g-recaptcha-response');
			if (!empty($recaptcha) && 
				$this->_provider->validate($recaptcha, $this->_helper->getSecretKey())) {
				return;
			}						
			$this->redirect(
				$observer->getEvent()->getControllerAction(), 
				$action
			);		
		}
    } 
    
    /**
     * Redirect to action
     *
     * @param Action $controller
     * @param string $action
     * @return void
     */
    public function redirect($controller, $action)
    {

        /* IDS - Added */
        if($action == 'review_product_post') {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $reviewSession = $objectManager->get('\Magento\Framework\Session\Generic'); /* @var $reviewSession \Magento\Framework\Session\Generic */
            $redirectUrl = $reviewSession->getRedirectUrl(true);
        } else {
            $redirectUrl = $this->_helper->getRedirectUrl($action);
        }
        /* END IDS - Added */

		$this->_messageManager->addError(
			new Phrase('There was an error with the reCAPTCHA code, please try again.')
		);					
		$this->_actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
		$this->_redirect->redirect(
			$controller->getResponse(),
            $redirectUrl
		);
    }        
}  
