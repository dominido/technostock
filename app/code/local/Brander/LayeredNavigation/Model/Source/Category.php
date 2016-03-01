<?php

class Brander_LayeredNavigation_Model_Source_Category extends Varien_Object
{
    public function toOptionArray()
    {
        $hlp = Mage::helper('brander_layerednavigation');
        return array(
            array('value' => Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Category::DT_DEFAULT,     'label' => $hlp->__('Default')),
            array('value' => Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Category::DT_DROPDOWN,    'label' => $hlp->__('Dropdown')),
            array('value' => Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Category::DT_WSUBCAT,     'label' => $hlp->__('With Sub-Categories')),
            array('value' => Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Category::DT_STATIC2LVL,  'label' => $hlp->__('Static 2-Level Tree')),
            array('value' => Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Category::DT_ADVANCED,    'label' => $hlp->__('Advanced Categories')),
        );
    }
}