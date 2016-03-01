<?php
class Brander_Cms_Block_Adminhtml_Helper_Form_Image extends Brander_AdminForms_Block_Adminhtml_Helper_Form_Image
{
    protected function _getUrl()
    {
        $url = 'adminforms/' . Mage::getModel('cmsadvanced/config')->getDefaultEntityType()  . parent::_getUrl();

        return $url;
    }
}
