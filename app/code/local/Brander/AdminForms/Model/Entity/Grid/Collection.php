<?php
class Brander_AdminForms_Model_Entity_Grid_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('adminforms/grid');
    }
}