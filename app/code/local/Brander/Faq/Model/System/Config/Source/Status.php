<?php
class Brander_Faq_Model_System_Config_Source_Status extends Brander_AdminForms_Model_Config_Source_Options
{
    public function toOptionArray($addEmpty = true) {
        $options = array();

        $options[] = array(
            'label' => Mage::helper('brander_faq')->__("New"),
            'value' => Brander_Faq_Model_Faq::BRANDER_FAQ_STATUS_NEW
        );

        $options[] = array(
            'label' => Mage::helper('brander_faq')->__("Approved"),
            'value' => Brander_Faq_Model_Faq::BRANDER_FAQ_STATUS_APPROVED
        );

        $options[] = array(
            'label' => Mage::helper('brander_faq')->__("Canceled"),
            'value' => Brander_Faq_Model_Faq::BRANDER_FAQ_STATUS_CANCALED
        );


        return $options;
    }
}