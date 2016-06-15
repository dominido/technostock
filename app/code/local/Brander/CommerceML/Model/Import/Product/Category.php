<?php

class Brander_CommerceML_Model_Import_Product_Category
{
    protected $_categories = array();

    public function push($id, $path)
    {
        if (!$this->hasData($id)) {
            $this->setData($id, $params);
        }
        return $this;
    }

    public function getCategory($id)
    {
        if (array_key_exists($id, $this->_categories)) {
            return $this->_categories[$id];
        } else {
            
        }
        return null;
    }
}