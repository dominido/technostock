<?php
class Brander_Preorder_Block_Adminhtml_Renderer_ProductGrid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        $product = Mage::getModel('catalog/product')->load($value);
        $name = $product->getName();
        $url = Mage::getModel('adminhtml/url')->getUrl('adminhtml/catalog_product/edit/id/'.$value);

        return '<a href="'.$url.'" target="_blank">'.$name.'</a>';

    }
}