<?php

class Brander_SearchAutocomplete_Model_System_Config_Source_Cms_Search_Result_Fields {

    protected $_options;

    public function toOptionArray() {
        if (!$this->_options) {
            $this->_options = array(
                array('value' => 'cms_title', 'label' => Mage::helper('searchautocomplete')->__('CMS Title')),
                array('value' => 'cms_description', 'label' => Mage::helper('searchautocomplete')->__('CMS Description'))
            );
        }
        return $this->_options;
    }

}