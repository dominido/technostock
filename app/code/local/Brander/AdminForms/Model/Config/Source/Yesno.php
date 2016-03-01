<?php

class Brander_AdminForms_Model_Config_Source_Yesno extends Mage_Adminhtml_Model_System_Config_Source_Yesno{

    const YES = 1;
    const NO  = 0;

    protected $_options;

    public function getAllOptions(){
        if (is_null($this->_options)){
            $this->_options = $this->toOptionArray();
        }
        return $this->_options;
    }

    public function getOptions(){
        $options = array();
        foreach ($this->toOptionArray() as $option){
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    public function setAttribute($attribute){
        // stub?
        return $this;
    }


}
