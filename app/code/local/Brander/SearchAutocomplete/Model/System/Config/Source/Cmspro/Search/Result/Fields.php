<?php

class Brander_SearchAutocomplete_Model_System_Config_Source_Cmspro_Search_Result_Fields {

    protected $_options;

    public function toOptionArray() {
        if (!$this->_options) {
            $this->_options = array(
                array('value' => 'cmspro_title', 'label' => Mage::helper('searchautocomplete')->__('Cms Pro Title')),
                array('value' => 'cmspro_summary', 'label' => Mage::helper('searchautocomplete')->__('Cms Pro Summary')),
                array('value' => 'cmspro_content', 'label' => Mage::helper('searchautocomplete')->__('Cms Pro Content')),
            );
        }
        return $this->_options;
    }

}