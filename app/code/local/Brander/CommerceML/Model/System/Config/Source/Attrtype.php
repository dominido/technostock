<?php

class Brander_CommerceML_Model_System_Config_Source_Attrtype
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'select', 'label' => 'Dropdown'),
            array('value' => 'text', 'label' => 'Text Field'),
            array('value' => 'boolean', 'label' => 'Yes/No'),
            array('value' => 'multiselect', 'label' => 'Multiple Select'),
            //array('value' => 'textarea', 'label' => 'Text Area'),
            //array('value' => 'date', 'label' => 'Date'),
            //array('value' => 'price', 'label' => 'Price'),
            //array('value' => 'media_image', 'label' => 'Media Image'),
            //array('value' => 'weee', 'label' => 'Fixed Product Tax'),
        );
    }
}










