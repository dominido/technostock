<?php

class Brander_CommerceML_Model_Logger extends Mage_Core_Model_Abstract
{
    /**
     * logging methos
     * @param string $data : log content
     * @param string $type : log type
     */
    public function log($data, $type)
    {
        Mage::log("$type:$data",null,'magmi.log',true);
    }
}