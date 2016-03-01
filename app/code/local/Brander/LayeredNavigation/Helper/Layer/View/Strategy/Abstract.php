<?php

abstract class Brander_LayeredNavigation_Helper_Layer_View_Strategy_Abstract
{
    /** @var Mage_Catalog_Block_Layer_Filter_Abstract */
    protected $filter;

    /** @var Brander_LayeredNavigation_Block_Catalog_Layer_View */
    protected $layer;

    abstract protected function setTemplate();
    abstract protected function setPosition();
    abstract protected function setCollapsed();
    abstract protected function setHasSelection();

    public function setFilter(Mage_Catalog_Block_Layer_Filter_Abstract $filter)
    {
        $this->filter = $filter;
    }

    public function setLayer(Brander_LayeredNavigation_Block_Catalog_Layer_View $layer)
    {
        $this->layer = $layer;
    }

    public function prepare()
    {
        $this->filter->setTemplate($this->setTemplate());
        $this->filter->setCollapsed($this->setCollapsed());
        $this->filter->setHasSelection($this->setHasSelection());
        $this->filter->setPosition($this->setPosition());
    }

    public function getIsExcluded()
    {
        return false;
    }

    protected function getCurrentCategoryId()
    {
        return $this->layer->getLayer()->getCurrentCategory()->getId();
    }

    protected function isCollapseEnabled()
    {
        return Mage::getStoreConfig('brander_layerednavigation/general/enable_collapsing');
    }

    protected function _getDataHelper()
    {
        /** @var Brander_LayeredNavigation_Helper_Data $helper */
        $helper = Mage::helper('brander_layerednavigation');
        return $helper;
    }
}
