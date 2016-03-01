<?php

class Brander_LayeredNavigation_Helper_Layer_View_Strategy_Price extends Brander_LayeredNavigation_Helper_Layer_View_Strategy_Abstract
{
    public function prepare()
    {
        parent::prepare();

        $this->filter->setDisplayType(Mage::getStoreConfig('brander_layerednavigation/general/price_type'));
        $this->filter->setSliderType(Mage::getStoreConfig('brander_layerednavigation/general/slider_type'));

        $step = Mage::getStoreConfig('brander_layerednavigation/general/slider_step');
        if ($step <= 0) {
            $step = 1.0;
        }
        $this->filter->setSliderDecimal($step);

        $currencySign = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
        $this->filter->setValueLabel($currencySign);

        $this->filter->setValuePlacement('after');
        $this->filter->setFromToWidget(Mage::getStoreConfig('brander_layerednavigation/general/price_from_to'));
        $this->filter->setAttributeCode('price');
        $this->filter->setSeoRel($this->_getDataHelper()->getSeoPriceRelNofollow());
        $this->filter->setData('hide_counts', !Mage::getStoreConfig('catalog/layered_navigation/display_product_count'));
    }

    protected function setTemplate()
    {
        return 'brander/layerednavigation/price.phtml';
    }

    protected function setPosition()
    {
        return $this->filter->getAttributeModel()->getPosition();
    }

    protected function setHasSelection()
    {
        return Mage::app()->getRequest()->getParam('price');
    }

    protected function setCollapsed()
    {
        return $this->isCollapseEnabled() && Mage::getStoreConfig('brander_layerednavigation/general/price_collapsed');
    }

    public function getIsExcluded()
    {
        if (parent::getIsExcluded()) {
            return true;
        }
        $excludeCats = explode(',', Mage::getStoreConfig('brander_layerednavigation/general/price_exclude'));
        $helper = Mage::helper('brander_layerednavigation');
        if (in_array( $helper->getCurrentCategory()->getId() ,$excludeCats ) ){
            return true;
        }
        return false;
    }
}
