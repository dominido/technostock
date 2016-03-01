<?php

class Brander_Faq_Block_ProductFaq extends Mage_Core_Block_Template
{

    public function getFaqCollection()
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::registry('current_product');
        if($product){
            $collection = Mage::helper('adminforms')->getCollection('branderfaqentities')
                              ->addAttributeToFilter('status', Brander_Faq_Model_Faq::BRANDER_FAQ_STATUS_APPROVED)
                              ->addAttributeToSort('position', 'ASC')
                              ->addAttributeToSort('entity_id', 'ASC')
                              ->addAttributeToFilter('entity_id',array('in'=>explode(',',$product->getFaqQuestions())));
            return $collection;
        }
        return null;
    }
}