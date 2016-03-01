<?php
class Brander_AdminForms_Block_Adminhtml_Helper_Form_Image extends Varien_Data_Form_Element_Image
{
    /*public function getElementHtml()
    {
        $html = '';

        if ($this->getValue()) {
            $url = $this->_getUrl();

            if( !preg_match("/^http\:\/\/|https\:\/\//", $url) ) {
                $url = Mage::getBaseUrl('media') . $url;
            }

            $html = '<a href="'.$url.'" onclick="imagePreview(\''.$this->getHtmlId().'_image\'); return false;"><img src="'.$url.'" id="'.$this->getHtmlId().'_image" title="'.$this->getValue().'" alt="'.$this->getValue().'" height="22" width="22" class="small-image-preview v-middle" /></a> ';
        }
        $class = $this->getClass();
        if ($class) {
            $class .= ' ';
        }

        $class .= 'input-file';
    
        $html.= Varien_Data_Form_Element_Abstract::getElementHtml();
        $html.= $this->_getDeleteCheckbox();

        return $html;
    }*/
}
