<?php

class Brander_LayeredNavigation_Model_Source_Position extends Brander_LayeredNavigation_Model_Source_Abstract
{
    const LEFT = 'left';
    const TOP = 'top';
    const BOTH = 'both';

    public function toOptionArray()
    {
        $hlp = Mage::helper('brander_layerednavigation');
        return array(
            array('value' => self::LEFT, 'label' => $hlp->__('Sidebar')),
            array('value' => self::TOP,  'label' => $hlp->__('Top')),
            array('value' => self::BOTH, 'label' => $hlp->__('Both')),
        );
    }
    
}