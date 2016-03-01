<?php

class Brander_SearchAutocomplete_Model_Mysql4_Fulltext_Cmspro_Collection extends MW_Cmspro_Model_Mysql4_News_Collection {
    
    public function setOrder($attribute, $dir = 'DESC') {
        if ('relevance' == $attribute) {
            $this->getSelect()->order("relevance $dir");
        } else {
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }

    public function addSearchFilter($query) {
        Mage::getSingleton('searchautocomplete/fulltext')->prepareResult();

        $this->getSelect()->joinInner(
                array('search_result' => $this->getTable('searchautocomplete/cmspro_result')),
                $this->getConnection()->quoteInto(
                        'search_result.`news_id`=main_table.`news_id` AND search_result.`query_id` = ?',
                        $this->_getQuery()->getId()
                ),
                array('relevance' => 'relevance')
        );
        return $this;
    }
    
    protected function _getQuery() {
        return Mage::helper('catalogSearch')->getQuery();
    }

}