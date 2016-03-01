<?php
class Brander_AdminForms_Model_Varien_Image extends Varien_Image
{
    protected function _getAdapter($adapter=null)
    {
        if( !isset($this->_adapter) ) {
            $this->_adapter = Mage::getModel('adminforms/varien_image_adapter_gd2');
        }
        return $this->_adapter;
    }

    public function adaptiveResize($width, $height = null)
    {
        return $this->_getAdapter()->adaptiveResize($width, $height);
    }
}
