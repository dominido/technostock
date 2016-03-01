<?php
class Brander_Cms_Model_Entity_Attribute_Backend_Grid extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function afterLoad($object)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();

        $collection = Mage::helper('adminforms')->getCollection($attributeCode, '*', false)->addAttributeToFilter('page_id', $object->getId());

        $object->setData($attributeCode, $collection);
        
        return $this;
    }
}
