<?php

class Brander_NewPost_Model_Resource_Warehouse extends Mage_Core_Model_Resource_Db_Abstract
{
	protected function _construct()
	{
		$this->_init('brander_newpost/warehouse', 'warehouse_id');
	}
} 