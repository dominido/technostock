<?php

class Brander_CommerceML_Model_Abstract extends Varien_Object
{
    protected $_isDebug = false;

    /*public function _construct()
    {
        $this->_data = array(
            'limit'                         => 0,
            'import_source_file'            => 'import.xml',
            'offers_source_file'            => 'offers.xml',
            'product_attributes_source'     => 'import', // import|offers
            'without_product_attributes'    => false,

        );
    }*/

    public function getSettings()
    {
        return Mage::helper('brandercml/settings');
    }

    public function log($message = null, $isError = true)
    {
        if ($message) {
            $filename = 'brandercml' . ($isError ? '_error' : '') . '.log';
            Mage::log($message, null, $filename, true);
        }
    }

    public function isDebugMode($mode = null)
    {
        if ($mode === null) {
            return $this->_isDebug;
        } else {
            $this->_isDebug = (bool)$mode;
        }
        return $this;
    }
}