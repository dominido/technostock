<?php

class Brander_CommerceML_Model_Category_Attribute_Backend_Urlkey extends Mage_Catalog_Model_Category_Attribute_Backend_Urlkey
{
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();

        $urlKey = $object->getData($attributeName);
        if ($urlKey === false) {
            return $this;
        }
        if ($urlKey=='') {
            $urlKey = $object->getName();
        }
        $urlKey = Mage::helper('catalog/product_url')->format($urlKey);
        $object->setData($attributeName, $urlKey);

        return $this;
    }
}
