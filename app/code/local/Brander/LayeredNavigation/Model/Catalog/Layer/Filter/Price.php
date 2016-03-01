<?php
/**
 * @author
 * @copyright Copyright (c) 2015
 * @package Brander_LayeredNavigation
 */  
class Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Price extends Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Price_Price17ce
{
    /**
     * Display Types
     */
    const DT_DEFAULT    = 0;
    const DT_DROPDOWN   = 1;
    const DT_FROMTO     = 2;
    const DT_SLIDER     = 3;

    public function _srt($a, $b)
    {
        $res = ($a['pos'] < $b['pos']) ? -1 : 1;
        return $res;
    }

    protected function _getCustomRanges()
    {
        $ranges = array();
        $collection = Mage::getModel('brander_layerednavigation/range')->getCollection()
            ->setOrder('price_frm','asc')
            ->load();
            
        $rate = Mage::app()->getStore()->getCurrentCurrencyRate(); 
        foreach ($collection as $range){
            $from = $range->getPriceFrm()*$rate;
            $to = $range->getPriceTo()*$rate;
            
            $ranges[$range->getId()] = array($from, $to);
        }
        
        if (!$ranges){
            echo "Please set up Custom Ranges in the Admin > Catalog > Improved Navigation > Ranges";
            exit;
        }
        
        return $ranges;
    }

    public function calculateRanges()
    {
        return (Mage::getStoreConfig('brander_layerednavigation/general/price_type') == self::DT_DEFAULT
            || Mage::getStoreConfig('brander_layerednavigation/general/price_type') == self::DT_DROPDOWN);
    }
    
    public function hideAfterSelection()
    {
        if (Mage::getStoreConfig('brander_layerednavigation/general/price_from_to')){
            return false;
        }
        if (Mage::getStoreConfig('brander_layerednavigation/general/price_type') == self::DT_SLIDER){
            return false;
        }
        return true;
    }

    public function getItemsCount()
    {
        $cnt = parent::getItemsCount();
        $checkForOne = $this->calculateRanges() && Mage::getStoreConfig('brander_layerednavigation/general/hide_one_value');

        return ($cnt == 1 && $checkForOne) ? 0 : $cnt;
    }
}