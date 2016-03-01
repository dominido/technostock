<?php

class Brander_NewPost_Model_City extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init('brander_newpost/city');
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
		return Mage::getSingleton('core/resource')->getTableName('brander_newpost/city');
	}

	public function saveRows($cities)
	{
		$sql = 'INSERT INTO '.$this->getTableName().' (city_id, locale_id, name) VALUES ';
		foreach($cities as $city)
		{
			$sql .= '('.$city->id.', 1, '.$this->_getConnection()->quote($city->nameRu).'), ';
			$sql .= '('.$city->id.', 2, '.$this->_getConnection()->quote($city->nameUkr).'), ';
		}
		$sql = substr($sql, 0, -2);
		$this->_getConnection()->query($sql);
	}

	public function getAdminOptionArray()
	{
		$options = array();
		$collection = $this->getCollection()
			->addFieldToFilter('locale_id', Mage::helper('brander_newpost')->getAdminLocaleId());
		foreach ($collection as $city)
		{
			$options[$city->getCityId()] = $city->getName();
		}

		asort($options);
		return $options;
	}

	public function getOptionArray()
	{
		$options = array();
		$collection = $this->getCollection()
			->addFieldToFilter('locale_id', Mage::getModel('brander_newpost/locale')->getLocaleIdByStore());
		foreach ($collection as $city)
		{
			$options[$city->getCityId()] = $city->getName();
		}

		asort($options);
		return $options;
	}
}