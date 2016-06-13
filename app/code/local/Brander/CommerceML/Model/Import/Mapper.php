<?php

class Brander_CommerceML_Model_Import_Mapper
{
    const PRODUCT_ATTRIBUTES_MAP = 'brandercml/mapper/attributes_map';

    protected $_values;

    public function __construct()
    {
        $this->prepareMapData();
    }

    public function map(&$map)
    {
        if (isset($this->_values[$map])) {
            $map = $this->_values[$map];
            return true;
        }
        return false;
    }

    public function getMap($str)
    {
        if (isset($this->_values[$str])) {
            return $this->_values[$str];
        }
        return $str;
    }

    protected function prepareMapData()
    {
        if (!$this->_values) {
            $this->_values = array();
            if ($values = Mage::getStoreConfig(self::PRODUCT_ATTRIBUTES_MAP)) {
                foreach (explode(',', $values) as $value) {
                    $val = explode(':', $value);
                    if (count($val) == 2) {
                        $this->_values[trim($val[0])] = trim($val[1]);
                    }
                }
            }
        }
        return $this->_values;
    }
}