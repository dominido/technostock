<?php

class Brander_CustomerCallbacks_Model_Callbacks extends Mage_Core_Model_Abstract
{
    protected function _construct(){
       $this->_init("brander_customercallbacks/callbacks");
    }

    protected function _beforeSave()
    {
        if ($this->isObjectNew()) {
            $now = Mage::getSingleton('core/date')->gmtDate();
            $this->setCreatedAt($now);
        }
        return $this;
    }
}
	 