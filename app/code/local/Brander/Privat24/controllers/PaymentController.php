<?php

class Brander_Privat24_PaymentController extends Mage_Core_Controller_Front_Action
{
    /**
     * Redirect to Privat24
     */
    public function redirectAction()
    {
		$session = Mage::getSingleton('checkout/session');
        $session->setPrivat24QuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('privat24/redirect')->toHtml());
        $session->unsQuoteId();
        $session->unsRedirectUrl();
    }

    /**
     * Customer return processing
     */
    public function returnAction()
    {
		$session = Mage::getSingleton('checkout/session');
        if($privat24QuoteId = $session->getPrivat24QuoteId()){
			$session->setQuoteId($privat24QuoteId);
		}
		Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
		if($request = Mage::app()->getRequest()->getPost()){
			$paymentMethod = Mage::getModel('privat24/redirect');
			if (is_array($request) && $paymentMethod->validateRequest($request)) {
				if(isset($request['payment'])){
					$payment = $request['payment'];
					parse_str($payment, $output);
					if(is_array($output) && count($output) > 0){
						if(isset($output['state'])){
							$state = $output['state'];
							if($state == 'fail'){
								$this->_redirect('privat24/fail/index', array('_secure'=>true));
								return;
							}
						}
					}
				}
			}
		} 		
		$this->_redirect('checkout/onepage/success', array('_secure'=>true));
    }

    /**
     * Background notifications
     */
    public function returnapiAction()
    {
		$request = Mage::app()->getRequest()->getPost();
		
        $paymentMethod = Mage::getModel('privat24/redirect');

        if (!is_array($request) || count($request) < 1 || !$paymentMethod->validateRequest($request)) {
            $this->_redirect('');
			return;
        }
	
		$ok = $paymentMethod->saveOrderState($request);
		
		$this->_redirect('');
		return ;
    }
}