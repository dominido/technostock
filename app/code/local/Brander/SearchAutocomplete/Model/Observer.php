<?php
class Brander_SearchAutocomplete_Model_Observer{

    public function regenerateCmsPageIndex($observer) {

        $page = $observer->getEvent()->getObject();
        $pageId = null;
        if(is_object($page))
        {
            $pageId = $page->getId();
        }
        Mage::getModel('searchautocomplete/fulltext')->
                regenerateIndex(null, $pageId)->
                resetSearchResults();
        return $this;
    }

    public function addCategoryToSearchIndex($observer) {
        $category = $observer->getEvent()->getCategory();
        if (!$category || !$category->getId()) return false;

        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();

        $connection->delete($tablePrefix . 'catalogsearch_category_fulltext', 'category_id = ' . $category->getId() . ' AND store_id = ' . $category->getStoreId());
        if ($category->getIsActive() && $category->getName()!='') {
            $data = array(
                'category_id' => $category->getId(),
                'data_index' => $category->getName(),
                'store_id' => $category->getStoreId()
            );

            $connection->insert($tablePrefix . 'catalogsearch_category_fulltext', $data);
        }
    }

    public function removeCategoryFromSearchIndex($observer) {
        $category = $observer->getEvent()->getCategory();
        if (!$category || !$category->getId()) return false;

        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();

        $connection->delete($tablePrefix . 'catalogsearch_category_fulltext', 'category_id = ' . $category->getId() . ' AND store_id = ' . $category->getStoreId());
    }


    public function regenerateStoreIndex($observer) {
        $store = $observer->getEvent()->getStore();
        if (!isset($store)) {
            $store = $observer->getEvent()->getObject();
        }

        if (isset($store)) {
            $storeId = $store->getId();
            Mage::getModel('searchautocomplete/fulltext')->regenerateIndex($storeId);
        }

        return $this;
    }

    public function cleanCmsPageIndex($observer) {
        $page = $observer->getEvent()->getObject();

        Mage::getModel('searchautocomplete/fulltext')
                ->cleanIndex(null, $page->getId())
                ->resetSearchResults();

        return $this;
    }


    public function catalogsearchIndexProcessStart($observer) {
        $event = $observer->getEvent();
        //$this->regenerateCmsPageIndex($observer);
        if (is_null($event->getStoreId()) && is_null($event->getProductIds())) {
            Mage::getModel('searchautocomplete/fulltext')->regenerateIndex();
        }
        return $this;
    }
    public function catalogControllerProductInit($observer) {
        $helper = Mage::helper('searchautocomplete');
        if ($helper->getLastQueryText()) {
            Mage::helper('catalog/output')->addHandler('productAttribute', Mage::getModel('searchautocomplete/highlight'));
        }
        return $this;
    }
    public function cmsPageLoadAfter($observer)
    {
        $helper = Mage::helper('searchautocomplete');
        $query = $helper->getLastQueryText();
        if ($query && ($page = $observer->getObject())) {
            $content = $page->getContent();
            $page->setContent($helper->highlightText($content, $query));
        }
        return $this;
    }

}