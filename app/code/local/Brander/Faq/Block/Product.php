<?php

class Brander_Faq_Block_Product extends Mage_Core_Block_Template
{
    public function getProduct(){
        $product = Mage::registry('current_product');
        if($product){
            return $product;
        }
        return null;
    }

    public function getSelectedPosts(){
        if($product = $this->getProduct()){
            $questions = $product->getFaqQuestions();
            return $questions;
        }
        return null;
    }
}