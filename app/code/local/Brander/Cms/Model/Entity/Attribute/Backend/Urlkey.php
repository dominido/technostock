<?php
class Brander_Cms_Model_Entity_Attribute_Backend_Urlkey extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();

        $urlKey = $object->getData($attributeName);
        if ($urlKey === false || $object->getParentId() === 1) {
            return $this;
        }
        if ($urlKey == '') {
            $urlKey = $object->getName();
        }

        $object->setData($attributeName, $object->formatUrlKey($urlKey));
        $object->unsetData('url_path');

        return $this;
    }
}
