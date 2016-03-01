<?php

class Brander_LayeredNavigation_Model_Source_Price extends Brander_LayeredNavigation_Model_Source_Abstract
{
    public function toOptionArray()
    {
        $hlp = Mage::helper('brander_layerednavigation');
        return array(
            array('value' => Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Price::DT_DEFAULT,    'label' => $hlp->__('Default')),
            array('value' => Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Price::DT_DROPDOWN,   'label' => $hlp->__('Dropdown')),
            array('value' => Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Price::DT_FROMTO,     'label' => $hlp->__('From-To Only')),
            array('value' => Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Price::DT_SLIDER,     'label' => $hlp->__('Slider')),
        );
    }
}