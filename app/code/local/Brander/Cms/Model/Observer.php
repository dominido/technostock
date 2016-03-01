<?php
class Brander_Cms_Model_Observer
{
    public function prepareBlockAdditionalElementTypes($observer)
    {
        $tabAttributes = $observer->getEvent()->getTabAttributes();

        $elementTypes = Mage::helper('cmsadvanced')->getConfig()->getElementTypes();

        foreach ($elementTypes as $type => $data) {
            if (isset($data['class'])) {
                $tabAttributes->addAdditionalElementTypes($type, Mage::getConfig()->getBlockClassName($data['class']));
            }
        }
    }
}
