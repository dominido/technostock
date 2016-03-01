<?php

class Brander_CmsHome_Model_Config_Source_Slideshow_Mode
{
    public function toOptionArray()
    {
		return array(
			array('value' => '0',	'label' => Mage::helper('brander_cmshome')->__('Regular only')),
			array('value' => '1',	'label' => Mage::helper('brander_cmshome')->__('Regular and Mobile(Pad)')),
            array('value' => '2',	'label' => Mage::helper('brander_cmshome')->__('Regular and Mobile(Phone and Pad)')),
        );
    }
}
