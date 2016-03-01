<?php

class Brander_NewPost_Block_Checkout_Onepage_Options extends Mage_Checkout_Block_Onepage_Shipping_Method_Available {
	public function getAddressList()
	{
		$city = Mage::helper('brander_newpost')->getCityFromCheckout();
		return Mage::getModel('brander_newpost/warehouse')->getArrayByCity($city);
	}

	public function __construct(){
		$this->setTemplate('brander/newpost/checkout/onepage/options.phtml');
	}
}