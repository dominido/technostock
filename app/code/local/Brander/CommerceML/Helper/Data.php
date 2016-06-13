<?php

class Brander_CommerceML_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function prepareAttributeValue($value)
    {
        return str_replace(array(' ', ' ', ','), array('', '', '.'), $value);
    }

    public function arrayUniqueArrayValues($arrayValue)
    {
        array_unique($arrayValue);

        return $arrayValue;
    }
}