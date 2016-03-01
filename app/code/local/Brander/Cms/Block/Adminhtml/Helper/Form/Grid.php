<?php
class Brander_Cms_Block_Adminhtml_Helper_Form_Grid extends Varien_Data_Form_Element_Abstract
{
    public function getElementHtml()
    {
        $name = $this->_getName();

        $blockGrid = Mage::helper('cmsadvanced')->getBlockGrid($name);
        
        $block = Mage::app()->getLayout()->createBlock($blockGrid, $name . '_grid', array('entity_type' => $name, 'block_key' => $name))
               ->setHtmlId($this->getHtmlId());

        return $block->toHtml();
    }

    protected function _getName()
    {
        $name = $this->getName();
        $matches = array();
        preg_match('/[a-zA-Z\_0-9]+\[(.*)]/', $name, $matches);

        $name = $matches[1];

        return $name;
    }

    public function getType()
    {
        return 'grid';
    }
}
