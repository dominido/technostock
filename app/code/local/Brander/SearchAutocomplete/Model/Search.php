<?php

class Brander_SearchAutocomplete_Model_Search extends Mage_Core_Model_Abstract {

    public function getRelevantCategoriesByQuery() {
        $categoryIds = $this->_getRelevantCategoryIdsByQuery();
        $collection = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToFilter('entity_id', array('in' => $categoryIds))
                ->addAttributeToSelect('name')
                ->setOrder('name', 'asc');
        
        if (!Mage::getStoreConfig(Mage_Catalog_Helper_Category_Flat::XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY)) {
            $collection = $collection->addAttributeToSelect('url');
        }
        return $collection;
    }
    
    protected function _getRelevantCategoryIdsByQuery() {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
        
        $query = Mage::helper('catalogsearch')->getEscapedQueryText();
        $query = mysql_escape_string($query);
        $storeId = Mage::app()->getStore()->getId();

        $limit = (int) Mage::helper('searchautocomplete')->getCatalogSearchResults();
        $rows = $connection->fetchAll("SELECT `category_id`, MATCH(`data_index`) AGAINST ('".$query."*' IN BOOLEAN MODE) AS relevance
                FROM `".$tablePrefix."catalogsearch_category_fulltext`
                WHERE (`store_id` = ".$storeId." OR `store_id` = 0) AND MATCH(`data_index`) AGAINST ('".$query."*' IN BOOLEAN MODE)
                ORDER BY relevance DESC
                LIMIT ".$limit
        );
        $categoryIds = array();
        foreach ($rows as $row) {
            $categoryIds[] = $row['category_id'];
        }
        return $categoryIds;
    }

    
    public function getCmsPageCollection() {
        return Mage::getResourceModel('searchautocomplete/fulltext_collection')
                ->addSearchFilter(Mage::helper('catalogSearch')->getEscapedQueryText())
                ->addStoreFilter(Mage::app()->getStore());
    }

    public function getBlogPostCollection() {        
        if ((string)Mage::getConfig()->getModuleConfig('AW_Blog')->active!='true' || !class_exists('AW_Blog_Model_Mysql4_Post_Collection')) return null;
        return Mage::getResourceModel('searchautocomplete/fulltext_blog_collection')
                ->addStoreFilter(Mage::app()->getStore())
                ->addSearchFilter(Mage::helper('catalogSearch')->getEscapedQueryText());
    }
    public function getCmsproNewsCollection() {        
        if ((string)Mage::getConfig()->getModuleConfig('MW_Cmspro')->active!='true' || !class_exists('MW_Cmspro_Model_Mysql4_News_Collection')) return null;
        return Mage::getResourceModel('searchautocomplete/fulltext_cmspro_collection')
                ->addSearchFilter(Mage::helper('catalogSearch')->getEscapedQueryText());
    }

}