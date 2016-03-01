<?php
class Brander_Preorder_Block_Adminhtml_Renderer_Product extends Varien_Data_Form_Element_Abstract
{
    protected $_element;

    public function getElementHtml()
    {
        /**
         * You can do all necessary customisations here
         *
         * You can use parent::getElementHtml() to get original markup
         * if you are basing on some other type and if it is required
         *
         * Use $this->getData('desired_data_key') to extract the desired data
         * E.g. $this->getValue() or $this->getData('value') will return form elements value
         */
        $product = Mage::getModel('catalog/product')->load($this->getValue());
        $name = $product->getName();
        $url = Mage::getModel('adminhtml/url')->getUrl('adminhtml/catalog_product/edit/id/'.$this->getValue());

        return '<a href="'.$url.'" target="_blank">'.$name.'</a>';
    }
}