<?php

class Brander_LayeredNavigation_Model_Mysql4_Value extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('brander_layerednavigation/value', 'value_id');
    }
}