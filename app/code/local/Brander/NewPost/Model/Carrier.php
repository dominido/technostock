<?php

class Brander_NewPost_Model_Carrier
	extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {
	protected $_code = 'brander_newpost';

	public function getFormBlock(){
		return 'brander_newpost/checkout_onepage_options';
	}
	/**
	 * Collect and get rates
	 *
	 * @param Mage_Shipping_Model_Rate_Request $request
	 * @return Mage_Shipping_Model_Rate_Result|bool|null
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		//Zend_Debug::dump( Mage::getStoreConfig('carriers/'.$this->_code.'/active'));
		if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
			return false;
		}
		$result = Mage::getModel('shipping/rate_result');

		$method = Mage::getModel('shipping/rate_result_method');
		$method->setCarrier($this->_code);
		$method->setMethod($this->_code);
		$method->setCarrierTitle($this->getConfigData('title'));
		$method->setMethodTitle($this->getConfigData('name'));
		$method->setPrice(Mage::getModel('brander_newpost/api')->getShippingCost());
		$method->setCost(0);
		$result->append($method);

		$checkout = Mage::getSingleton('checkout/session')->getQuote();
		$shipping = $checkout->getShippingAddress();

		if(!Mage::getModel('brander_newpost/city')->getCollection()
            ->addFieldToFilter('LOWER(name)', mb_strtolower($shipping->getCity(), 'UTF-8'))
			->addFieldToFilter('locale_id', Mage::getModel('brander_newpost/locale')->getLocaleIdByStore())
			->getSize()) {
			$error = Mage::getModel('shipping/rate_result_error');
			$error->setCarrier($this->_code);
			$error->setCarrierTitle($this->getConfigData('name'));
			$error->setErrorMessage($this->getConfigData('specificerrmsg'));
			$result->append($error);
		}
		return $result;
	}

	public function getAllowedMethods()
	{
		return array('brander_newpost'=>$this->getConfigData('name'));
	}
}
