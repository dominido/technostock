<?php

class Brander_LayeredNavigation_Helper_Layer_View_Strategy_Category extends Brander_LayeredNavigation_Helper_Layer_View_Strategy_Abstract
{
    public function prepare()
    {
        parent::prepare();

        $this->filter->setDisplayType(Mage::getStoreConfig('brander_layerednavigation/general/categories_type'));
    }

    protected function setTemplate()
    {
        return 'brander/layerednavigation/category.phtml';
    }

    protected function setPosition()
    {
        return Mage::getStoreConfig('brander_layerednavigation/general/categories_order');
    }

    protected function setHasSelection()
    {
        return false;
    }

    protected function setCollapsed()
    {
        return $this->isCollapseEnabled() && Mage::getStoreConfig('brander_layerednavigation/general/categories_collapsed');
    }

    public function getIsExcluded()
    {
        return $this->setPosition() == -1;
    }
}
