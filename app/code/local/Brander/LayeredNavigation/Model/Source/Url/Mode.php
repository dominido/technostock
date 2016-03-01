<?php

class Brander_LayeredNavigation_Model_Source_Url_Mode extends Varien_Object
{
    const MODE_DISABLED = 0;
    const MODE_MULTILEVEL = 1;
    const MODE_SHORT = 2;

    public function toOptionArray()
    {
        $hlp = Mage::helper('brander_layerednavigation');
        return array(
            array('value' => self::MODE_DISABLED, 'label' => $hlp->__('With GET Parameters')),
            array('value' => self::MODE_MULTILEVEL, 'label' => $hlp->__('Long with URL key')),
            array('value' => self::MODE_SHORT, 'label' => $hlp->__('Short without URL key')),
        );
    }
}
