<?php

class Brander_LayeredNavigation_Helper_Layer_View_Strategy_Rating extends Brander_LayeredNavigation_Helper_Layer_View_Strategy_Abstract
{

    public function prepare()
    {
        parent::prepare();

        $this->filter->setData('hide_counts', !Mage::getStoreConfig('catalog/layered_navigation/display_product_count'));
    }

    protected function setTemplate()
    {
        return 'brander/layerednavigation/attribute.phtml';
    }

    protected function setPosition()
    {
        return $this->filter->getPosition();
    }

    protected function setHasSelection()
    {
        return isset($_GET['rating']);
    }

    protected function setCollapsed()
    {
        return $this->isCollapseEnabled() && Mage::getStoreConfig('brander_layerednavigation/general/rating_collapsed');
    }
}
