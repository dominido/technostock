<?php

class Brander_NewPost_Model_Address extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init('brander_newpost/address');
	}

	public function truncate()
	{
		$this->_getConnection()->query("TRUNCATE table `"
			.$this->getTableName()."`");
	}

	protected function _getConnection()
	{
		return Mage::getSingleton('core/resource')->getConnection('core_write');
	}

	public function getTableName()
	{
		return Mage::getSingleton('core/resource')->getTableName('brander_newpost/address');
	}

	public function saveRows($addresses)
	{
		$sql = 'INSERT INTO '.$this->getTableName().' (warehouse_id, locale_id, name) VALUES ';
		foreach($addresses as $address)
		{
			$sql .= '('.$address->wareId.', 1, '.$this->_getConnection()->quote($address->addressRu).'), ';
			$sql .= '('.$address->wareId.', 2, '.$this->_getConnection()->quote($address->address).'), ';
		}
		$sql = substr($sql, 0, -2);
		$this->_getConnection()->query($sql);
	}
} 