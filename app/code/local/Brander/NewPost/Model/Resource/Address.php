<?php

class Brander_NewPost_Model_Resource_Address extends Mage_Core_Model_Resource_Db_Abstract
{
	protected function _construct()
	{
		$this->_init('brander_newpost/address', 'address_id');
	}
} 