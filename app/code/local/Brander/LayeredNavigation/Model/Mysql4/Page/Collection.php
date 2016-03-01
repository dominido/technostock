<?php

class Brander_LayeredNavigation_Model_Mysql4_Page_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('brander_layerednavigation/page');
        $this->setOrder('num', 'desc');
    }

    public function addStoreFilter()
    {
        $storeId = Mage::app()->getStore()->getId();
        $this->getSelect()->where('stores = "" OR stores REGEXP "(^|,)' . $storeId . '($|,)"');
    }

    public function addCategoryFilter($categoryId)
    {
        if (isset($categoryId)) {
            $categoryId = (int) $categoryId;
            $this->getSelect()->where('cats = "" OR cats REGEXP "(^|,)' . $categoryId . '($|,)"');
        }
    }
}