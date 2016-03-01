<?php


class Brander_Privat24_Block_Message extends Mage_Payment_Block_Form
{
    /**
     * Set template with message
     */
    protected function _construct()
    {
        $this->setTemplate('brander/privat24/message.phtml');
        parent::_construct();
    }
}