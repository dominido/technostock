<?php

class Brander_NewPost_Model_Api
{
	protected $_apiKey;
	public function __construct()
	{
		$this->_apiKey = Mage::helper("brander_newpost")->getApiKey();
	}

	protected function _sendRequest($xml)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://orders.novaposhta.ua/xml.php');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);

		return simplexml_load_string($response);
	}

	protected function _getCities()
	{
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><file/>');
		$xml->addChild('auth', $this->_apiKey);
		$xml->addChild('citywarehouses');
		$response = $this->_sendRequest($xml->asXML());

		if (!$response) {
			return false;
		}

		try {
			return $response->result->cities->city;
		}
		catch (Exception $e) {
			Mage::logException($e);
			return array();
		}
	}

	protected function _getWarehouses()
	{
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><file/>');
		$xml->addChild('auth', $this->_apiKey);
		$xml->addChild('warenhouse');
		$response = $this->_sendRequest($xml->asXML());

		if (!$response) {
			return false;
		}

		try {
			return $response->result->whs->warenhouse;
		}
		catch (Exception $e) {
			Mage::logException($e);
			return array();
		}
	}

	protected function _syncCities()
	{
		$cities = $this->_getCities();
		Mage::getModel('brander_newpost/city')->truncate();
		Mage::getModel('brander_newpost/city')->saveRows($cities);
		return true;
	}

	protected function syncWarehouses()
	{
		$warehouses = $this->_getWarehouses();
		Mage::getModel('brander_newpost/address')->truncate();
		Mage::getModel('brander_newpost/warehouse')->truncate();
		Mage::getModel('brander_newpost/address')->saveRows($warehouses);
		Mage::getModel('brander_newpost/warehouse')->saveRows($warehouses);
		return true;
	}

	public function processImport()
	{
		if ($this->_syncCities())
			return $this->syncWarehouses();
		else
			return false;
	}

	public function getShippingCost()
	{
		$cost = 0;
		if (Mage::getStoreConfig('carriers/brander_newpost/zero_cost')) {
			return $cost;
		}
		$cart = Mage::getModel('checkout/cart')->getQuote();
		foreach ($cart->getAllItems() as $item)
		{
			$cost += $item->getQty() * $this->_getShippingCostFromApi(
				Mage::helper('brander_newpost')->getSenderCity(),
				$cart->getShippingAddress()->getCity(),
				$item->getProduct()->getWeight(),
				$item->getProduct()->getWidth(),
				$item->getProduct()->getHeight(),
				$item->getProduct()->getDepth(),
				$item->getProduct()->getPrice());
		}

		return $cost;
	}

	protected function _getShippingCostFromApi($city_from, $city_to, $weight, $width, $height, $depth, $price)
	{
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><file/>');
		$xml->addChild('auth', $this->_apiKey);
		$count_price = $xml->addChild('countPrice');
		$count_price->addChild('senderCity', $city_from);
		$count_price->addChild('recipientCity', $city_to);
		$count_price->addChild('mass', $weight);
		$count_price->addChild('height', $height);
		$count_price->addChild('width', $width);
		$count_price->addChild('depth', $depth);
		$count_price->addChild('publicPrice', $price);
		$count_price->addChild('deliveryType_id', 4);
		$count_price->addChild('date', date('d.m.Y'));

		$response = $this->_sendRequest($xml->asXML());

		//Mage::log($response->asXML());
		//Mage::log($xml->asXML());

		if (!$response) {
			return 0;
		}

		try {
			return (string)$response->cost;
		}
		catch (Exception $e) {
			Mage::logException($e);
			return 0;
		}
	}
}
