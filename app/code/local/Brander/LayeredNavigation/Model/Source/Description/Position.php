<?php

class Brander_LayeredNavigation_Model_Source_Description_Position extends Brander_LayeredNavigation_Model_Source_Abstract
{
    const AFTER = 'after';
    const BEFORE = 'before';
    const REPLACE = 'replace';
    const DO_NOT_ADD = 'do-not-add';

    public function toOptionArray()
    {
        $hlp = Mage::helper('brander_layerednavigation');
        return array(
            array('value' => self::AFTER, 'label' => $hlp->__('After')),
            array('value' => self::BEFORE,  'label' => $hlp->__('Before')),
            array('value' => self::REPLACE, 'label' => $hlp->__('Replace')),
            array('value' => self::DO_NOT_ADD,  'label' => $hlp->__('Do Not Add')),
        );
    }
}
