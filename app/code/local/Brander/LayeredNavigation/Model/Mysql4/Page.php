<?php

class Brander_LayeredNavigation_Model_Mysql4_Page extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('brander_layerednavigation/page', 'page_id');
    }

    /**
     * @param int $categoryId
     * @return Brander_LayeredNavigation_Model_Page|null
     */
    public function getCurrentMatchedPage($categoryId)
    {
        $result = null;

        /** @var Brander_LayeredNavigation_Model_Mysql4_Page_Collection $collection */
        $collection = Mage::getModel('brander_layerednavigation/page')->getCollection();
        $collection->addStoreFilter();
        if ($categoryId) {
            $collection->addCategoryFilter($categoryId);
        }

        foreach ($collection as $page){
            /** @var Brander_LayeredNavigation_Model_Page $page */

            if ($page->matchCurrentFilters()) {
                $result = $page;
                break;
            }
        }

        return $result;
    }
}