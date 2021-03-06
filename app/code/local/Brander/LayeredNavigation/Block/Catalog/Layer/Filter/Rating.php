<?php

class Brander_LayeredNavigation_Block_Catalog_Layer_Filter_Rating extends Mage_Catalog_Block_Layer_Filter_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('brander/layerednavigation/attribute.phtml');
        $this->_filterModelName = 'brander_layerednavigation/catalog_layer_filter_rating';
    }

    /**
     * Get Sort Order for filter
     * @return number
     */
    public function getPosition()
    {
        $countItems = count($this->getItemsAsArray());
        if ($countItems == 0
            || ($countItems == 1
                && Mage::getStoreConfig(
                    'brander_layerednavigation/general/hide_one_value'
                ))
        ) {
            return -1;
        }

        return Mage::getStoreConfig('brander_layerednavigation/general/rating_filter_pos');
    }

    public function getDisplayType()
    {
        return 0;
    }

    public function getItemsAsArray()
    {
        $items = array();
        foreach (parent::getItems() as $itemObject){

            $item = array();
            $item['url']   = $this->escapeHtml($itemObject->getUrl());
            $item['label'] = $itemObject->getLabel();
            $item['count'] = '';
            $item['countValue']  = $itemObject->getCount();

            /** @todo Fix item counts */
            if (!$this->getHideCounts()) {
                $item['count']  = ' (' . $itemObject->getCount() . ')';
            }

            $item['css'] = 'layerednavigation-attr';
            if (in_array($this->getDisplayType(), array(1,3))) //dropdown and images
                $item['css'] = '';

            if ($itemObject->getOptionId() == $this->getRequestValue()){
                $item['css'] .= '-selected';
                if (3 == $this->getDisplayType()) //dropdown
                    $item['css'] = 'selected';
            }

            $item['rel'] = $this->getSeoRel() ? ' rel="nofollow" ' : '';

            if ($item['countValue']) {
                $items[] = $item;
            }
        }

        return $items;
    }

    public function getRequestValue()
    {
        return Mage::app()->getRequest()->getParam('rating');
    }

}