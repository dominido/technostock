<?php

class Brander_CommerceML_Helper_Settings extends Mage_Core_Helper_Abstract
{
    public function getConfig($name)
    {
        return Mage::getStoreConfig('brandercml/' . $name);
    }

    
}