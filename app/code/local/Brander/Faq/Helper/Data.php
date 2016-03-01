<?php
class Brander_Faq_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getFaqQuestions($pageId) {
        return Mage::getModel('brander_faq/faq')->getFaqCollection($pageId);
    }

    public function getVideoFaqQuestions($pageId) {
        return Mage::getModel('brander_faq/videoFaq')->getVideoFaqCollection($pageId);
    }

    public function getFormUrl() {
        return Mage::getUrl('faq/index/post');
    }

    public function getFaqLimit()
    {
        return Mage::getStoreConfig('brander_faq/faq_settings/show_more_limit');
    }

}