<?php
class Brander_AdminForms_Model_Config_Source_Options extends Varien_Object
{
    public function getAllOptions(){
        if (!$this->_options){
            $this->_options = $this->toOptionArray();
        }
        return $this->_options;
    }
    /**
     * @return array
     *      array(
     *          array('value' => value, 'label' => label)
     *      )
     */
    public function toOptionArray()
    {
        return array();
    }

    public function getOptions(){
        $options = array();
        foreach ($this->toOptionArray() as $option){
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    public function getOptionText($optionId)
    {
        $options = $this->getAllOptions();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
