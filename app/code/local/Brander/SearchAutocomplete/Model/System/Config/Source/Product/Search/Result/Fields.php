<?php


class Brander_SearchAutocomplete_Model_System_Config_Source_Product_Search_Result_Fields{

    protected $_options;

    public function toOptionArray() {
        if (!$this->_options) {
            $this->_options = array(
                array('value' => 'product_name', 'label' => Mage::helper('searchautocomplete')->__('Product Name')),
                array('value' => 'sku', 'label' => Mage::helper('searchautocomplete')->__('SKU')),
                array('value' => 'product_image', 'label' => Mage::helper('searchautocomplete')->__('Product Image')),
                array('value' => 'reviews_rating', 'label' => Mage::helper('searchautocomplete')->__('Reviews Rating')),
                array('value' => 'short_description', 'label' => Mage::helper('searchautocomplete')->__('Short Description')),
                array('value' => 'description', 'label' => Mage::helper('searchautocomplete')->__('Description')),
                array('value' => 'price', 'label' => Mage::helper('searchautocomplete')->__('Price')),
                array('value' => 'add_to_cart_button', 'label' => Mage::helper('searchautocomplete')->__('Add to Cart Button')),
            );
        }
        return $this->_options;
    }

}