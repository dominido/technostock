<?php

class Brander_Shop_Block_CatalogInventory_Stockqty_Type_Configurable extends Mage_CatalogInventory_Block_Stockqty_Type_Configurable
{
    protected function _getProduct()
    {
        $product = new Mage_Catalog_Model_Product();
        if ($this->getProduct()) {
            $product = $this->getProduct();
        }
        return $product;
    }
}