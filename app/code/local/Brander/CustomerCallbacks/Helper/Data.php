<?php
class Brander_CustomerCallbacks_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function isVisibleInHeader()
    {
        return Mage::getStoreConfig('customercallbacks/settings/show_in_header');
    }

    public function isVisibleInFooter()
    {
        return Mage::getStoreConfig('customercallbacks/settings/show_in_footer');
    }

    public function getRequestMessage()
    {
        if ($message = Mage::getStoreConfig('customercallbacks/settings/request_message')) {
            return $message;
        }
        return $this->__('Leave your phone number and name. We will contact you shortly.');
    }

    public function getConfirmMessage()
    {
        if ($message = Mage::getStoreConfig('customercallbacks/settings/confirm_message')) {
            return $message;
        }
        return $this->__('Thank you for your request. We`ll call you back shortly.');
    }

    public function showCommentField()
    {
        if (Mage::getStoreConfig('customercallbacks/settings/show_comment_field')) {
            return true;
        }
        return false;
    }
}