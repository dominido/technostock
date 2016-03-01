<?php

class Brander_LayeredNavigation_Block_Featured extends Mage_Core_Block_Template
{
    public function getItems()
    {
        $items = array();
        // get filter ID by attribute code
        $id = Mage::getResourceModel('brander_layerednavigation/filter')
            ->getIdByCode($this->getAttributeCode());
        if ($id){
            $items = Mage::getResourceModel('brander_layerednavigation/value_collection')
                ->addFieldToFilter('is_featured', 1)
                ->addFieldToFilter('filter_id', $id)
                ->addValue();

            if ($this->getRandom()){
                $items->setOrder('rand()');
            }
            else {
                $items->setOrder('featured_order', 'asc');
                $items->setOrder('value', 'asc');
                $items->setOrder('title', 'asc');
            }

            if ($this->getLimit()){
                $items->setPageSize(intVal($this->getLimit()));
            }

            /** @var Brander_LayeredNavigation_Helper_Url $urlHelper */
            $urlHelper = Mage::helper('brander_layerednavigation/url');
            $base = Mage::getBaseUrl('media') . '/layerednavigation/';
            foreach ($items as $item){
                /** @var Brander_LayeredNavigation_Model_Value $item */

                if ($item->getImgBig())
                    $item->setImgBig($base . $item->getImgBig());

                $item->setUrl($urlHelper->getOptionUrl($this->getAttributeCode(), $item->getOptionId()));
            }
        }
        return $items;
    }
}
