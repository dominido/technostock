<?php


class Brander_Faq_Model_System_Config_Source_Faqquestions extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        $collection = Mage::helper('adminforms')->getCollection('branderfaqentities');

        $options = array();

        foreach($collection as $option){
                $options[] = array(
                    'label' => $option->getQuestion(),
                    'value' => $option->getId()
                );
        }
        return $options;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}