<?php

class Brander_NewPost_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getApiKey()
	{
		return Mage::getStoreConfig("carriers/brander_newpost/api_key");
	}

	public function getSenderCity()
	{
		return Mage::getStoreConfig("general/store_information/city");
	}

	public function getAdminLocaleId()
	{
		switch (Mage::app()->getLocale()->getLocaleCode())
		{
			case 'uk_UA';
				return 2;
			case 'ru_RU':
				return 1;
			default:
				return 1;
		}
	}

	public function getCityFromCheckout()
	{
		$checkout = Mage::getSingleton('checkout/session')->getQuote();
		$shipping = $checkout->getShippingAddress();
		return $shipping->getCity();
	}
}