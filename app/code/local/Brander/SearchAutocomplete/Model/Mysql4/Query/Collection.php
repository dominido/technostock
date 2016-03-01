<?php

class Brander_SearchAutocomplete_Model_Mysql4_Query_Collection extends Mage_CatalogSearch_Model_Mysql4_Query_Collection
{
    public function setLimit($limit)
    {
        $this->getSelect()->limit($limit);        
        return $this;
    }
    
    public function toArray($arrRequiredFields = array())
    {
        $data = array();
        foreach ($this as $item) {
            $data[] = array(
                'title' => $item->getQueryText(),
                'num_of_results' => $item->getNumResults()
            );
        }
        return $data;
    }
    
    
}
