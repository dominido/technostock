<?php
class Brander_CustomerCallbacks_Model_Resource_Callbacks_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct(){
        $this->_init("brander_customercallbacks/callbacks");
    }
}
	 