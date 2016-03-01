<?php

class Brander_AdminForms_Model_Config_Source_Status extends Varien_Object{
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED  = 1;

    public function getAllOptions(){
        if (!$this->_options){
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

    public function toOptionArray($withEmpty = false){
        $hlp = Mage::helper('adminforms');
        $options = array(
            array('value'=>self::STATUS_ENABLED, 'label'=>$hlp->__('Enable')),
            array('value'=>self::STATUS_DISABLED, 'label'=>$hlp->__('Disable')),
        );
        if ($withEmpty){
            array_unshift($options, array(
                'value' => '',
                'label' => $hlp->__('-- Please Select --')
            ));
        }
        return $options;
    }
}
