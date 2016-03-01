<?php

class Brander_LayeredNavigation_Block_Adminhtml_Filter_Attribute_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $filter)
    {
        /** @var Brander_LayeredNavigation_Model_Filter $filter */

        $url = $this->helper('adminhtml')->getUrl('adminhtml/catalog_product_attribute/edit', array('attribute_id' => $filter->getAttributeId()));

        return $filter->getAttributeCode() . ' <a href="' . $url . '" style="float:right;"><button type="button">Edit Attribute</button></a>';
    }
}