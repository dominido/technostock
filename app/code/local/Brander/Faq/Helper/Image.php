<?php
class Brander_Faq_Helper_Image extends Mage_Core_Helper_Abstract
{
    public function getImageUrl($object)
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'adminforms' . DS . $object->getEntityType() . $object->getSmallImage();
    }
}