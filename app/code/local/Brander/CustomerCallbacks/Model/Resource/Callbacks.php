<?php
class Brander_CustomerCallbacks_Model_Resource_Callbacks extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init("brander_customercallbacks/callbacks", "id");
    }
}