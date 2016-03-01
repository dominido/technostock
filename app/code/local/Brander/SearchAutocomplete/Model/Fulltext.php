<?php


class Brander_SearchAutocomplete_Model_Fulltext extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $this->_init('searchautocomplete/fulltext');
    }

    public function regenerateIndex($storeId = null, $pageId = null) {        
        $this->getResource()->regenerateIndex($storeId, $pageId);
        return $this;
    }

    public function cleanIndex($storeId = null, $pageId = null) {
        $this->getResource()->cleanIndex($storeId, $pageId);
        return $this;
    }

    public function resetSearchResults() {
        $this->getResource()->resetSearchResults();
        return $this;
    }

    public function prepareResult($query = null) {
        if (!$query instanceof Mage_CatalogSearch_Model_Query) {
            $query = Mage::helper('catalogSearch')->getQuery();
        }
        $queryText = Mage::helper('catalogSearch')->getQueryText();
        if ($query->getSynonimFor()) {
            $queryText = $query->getSynonimFor();
        }
        $this->getResource()->prepareResult($this, $queryText, $query);
        return $this;
    }

    public function getSearchType($storeId = null) {
        return Mage::helper('searchautocomplete')->getSearchType($storeId);
    }

}