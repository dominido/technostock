<?php
/**
 * @author
 * @copyright Copyright (c) 2015
 * @package Brander_LayeredNavigation
 */
class Brander_LayeredNavigation_Block_Search_Layer extends Brander_LayeredNavigation_Block_Catalog_Layer_View
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        Mage::register('current_layer', $this->getLayer(), true);
    } 
    
    /**
     * Get attribute filter block name
     *
     * @return string
     */
    protected function _getAttributeFilterBlockName()
    {
        return 'catalogsearch/layer_filter_attribute';
    }

    /**
     * Get layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        return Mage::getSingleton('catalogsearch/layer');
    }

    
    public function canShowBlock()
    {

        $engine = Mage::helper('catalogsearch')->getEngine();

        if (method_exists($engine, 'isLayeredNavigationAllowed')){
            $allowed = $engine->isLayeredNavigationAllowed();
        } else {
            $allowed = $engine->isLeyeredNavigationAllowed();
        }

        return $allowed && parent::canShowBlock();
    }
}