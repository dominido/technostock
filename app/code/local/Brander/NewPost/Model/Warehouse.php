<?php

class Brander_NewPost_Model_Warehouse extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init('brander_newpost/warehouse');
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
		return Mage::getSingleton('core/resource')->getTableName('brander_newpost/warehouse');
	}

	public function saveRows($warehouses)
	{
		$sql = 'INSERT INTO '.$this->getTableName().' (warehouse_id, city_id, phone, max_weight_allowed,
			weekday_work_hours, weekday_receiving_hours, weekday_delivery_hours, saturday_work_hours,
			saturday_receiving_hours, saturday_delivery_hours, x, y) VALUES ';
		foreach($warehouses as $wh)
		{
			$sql .= "\n(".$wh->wareId.', '
				.$wh->city_id.', '
				.$this->_getConnection()->quote($wh->phone).', '
				.$this->_getConnection()->quote($wh->max_weight_allowed).', '
				.$this->_getConnection()->quote($wh->weekday_work_hours).', '
				.$this->_getConnection()->quote($wh->weekday_reseiving_hours).', '
				.$this->_getConnection()->quote($wh->weekday_delivery_hours).', '
				.$this->_getConnection()->quote($wh->saturday_work_hours).', '
				.$this->_getConnection()->quote($wh->saturday_reseiving_hours).', '
				.$this->_getConnection()->quote($wh->saturday_delivery_hours).', '
				.$this->_getConnection()->quote($wh->x).', '
				.$this->_getConnection()->quote($wh->y).'), ';
		}
		$sql = substr($sql, 0, -2);
		$this->_getConnection()->query($sql);
	}

	public function getFullCollection()
	{
		$collection = $this->getCollection();
		$collection->getSelect()
			->join(array('a' => Mage::getModel('brander_newpost/address')->getTableName()),
				'main_table.warehouse_id = a.warehouse_id',
				array('a.name', 'a.locale_id'));

		$collection->addFieldToFilter('locale_id', Mage::helper('brander_newpost')->getAdminLocaleId());
		return $collection;
	}

	public function getArrayByCity($city)
	{
		$collection = $this->getCollection();
		$collection->getSelect()
			->join(array('a' => Mage::getModel('brander_newpost/address')->getTableName()),
				'main_table.warehouse_id = a.warehouse_id',
				array('a.name'));

		$collection->getSelect()
			->join(array('c' => Mage::getModel('brander_newpost/city')->getTableName()),
				'main_table.city_id = c.city_id', '');

		$collection
			->addFieldToFilter('a.locale_id', Mage::getModel('brander_newpost/locale')->getLocaleIdByStore())
			->addFieldToFilter('c.locale_id', Mage::getModel('brander_newpost/locale')->getLocaleIdByStore())
            ->addFieldToFilter('LOWER(c.name)', mb_strtolower($city, 'UTF-8'));

		$options = array();
		foreach ($collection as $wh)
		{
			$options[$wh->getData('warehouse_id')] = $wh->getData('name');
		}
		return $options;
	}
}