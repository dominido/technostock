<?php

class Brander_SearchAutocomplete_Model_System_Config_Source_Product_Search_Result_Sort_Order {

    protected $_options;

    public function toOptionArray() {
        if (!$this->_options) {
            $this->_options = array(
                array('value' => 'price_asc', 'label' => Mage::helper('searchautocomplete')->__('Price Asc')),
                array('value' => 'price_desc', 'label' => Mage::helper('searchautocomplete')->__('Price Desc')),
                array('value' => 'relevance', 'label' => Mage::helper('searchautocomplete')->__('Relevance')),
                array('value' => 'name_asc', 'label' => Mage::helper('searchautocomplete')->__('Name A-Z')),
                array('value' => 'name_desc', 'label' => Mage::helper('searchautocomplete')->__('Name Z-A')),
            );
        }
        return $this->_options;
    }

}