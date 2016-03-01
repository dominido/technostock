<?php

class Brander_LayeredNavigation_Block_Search_Layer_Filter_Attribute extends Brander_LayeredNavigation_Block_Catalog_Layer_Filter_Attribute
{
    public function __construct()
    {
        parent::__construct();
        $this->_filterModelName = 'catalogsearch/layer_filter_attribute';  
    }
}