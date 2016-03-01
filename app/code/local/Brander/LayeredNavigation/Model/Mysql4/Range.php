<?php

class Brander_LayeredNavigation_Model_Mysql4_Range extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('brander_layerednavigation/range', 'range_id');
    }
}