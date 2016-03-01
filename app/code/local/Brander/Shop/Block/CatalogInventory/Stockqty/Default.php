<?php

class Brander_Shop_Block_CatalogInventory_Stockqty_Default extends Mage_CatalogInventory_Block_Stockqty_Default
{
    protected function _getProduct()
    {
        $product = new Mage_Catalog_Model_Product();
        if ($this->getProduct()) {
            $product = $this->getProduct();
        }
        return $product;
    }

    public function getStockQty()
    {
            $qty = 0;
            if ($stockItem = $this->_getProduct()->getStockItem()) {
                $qty = (float) $stockItem->getStockQty();
            }
        return $qty;
    }
}