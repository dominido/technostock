<?php
class Brander_Contacts_Block_Maps extends Mage_Core_Block_Template
{
    protected $_collection = null;

    public function getShopsCollection()
    {
        if(!$this->_collection){
            $this->_collection = Mage::helper('adminforms')->getCollection('shops_list')
                ->addAttributeToFilter('status', Brander_AdminForms_Model_Config_Source_Status::STATUS_ENABLED)
                ->addAttributeToSort('position', 'ASC');
        }
        return $this->_collection;
    }
}