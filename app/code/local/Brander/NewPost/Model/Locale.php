<?php

class Brander_NewPost_Model_Locale extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init('brander_newpost/locale');
	}

	public function getOptionArray()
	{
		$options = array();
		$collection = $this->getCollection();
		foreach ($collection as $locale)
		{
			$options[$locale->getId()] = $locale->getName();
		}

		asort($options);
		return $options;
	}

	public function getLocaleIdByStore()
	{
		$collection = $this->getCollection()
			->addFieldToFilter('magento_store_id', Mage::app()->getStore()->getId())
			->addFieldToFilter('is_active', 1)
			->getFirstItem();

		return ($collection->getLocaleId()) ? $collection->getLocaleId() : -1;
	}
}