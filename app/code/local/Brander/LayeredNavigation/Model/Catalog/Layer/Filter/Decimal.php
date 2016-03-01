<?php
/**
 * @author
 * @copyright Copyright (c) 2015
 * @package Brander_LayeredNavigation
 */


class Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Decimal extends Mage_Catalog_Model_Layer_Filter_Decimal
{
    private $_rangeSeparator = ',';
    private $_fromToSeparator = '-';

    protected $settings = null;

    public function getItemsCount()
    {
        $min = $this->getMinValue();
        $max = $this->getMaxValue();

        $noneVariant = is_null($min) || is_null($max);
        $oneHiddenVariant = ($min == $max) && Mage::getStoreConfig('brander_layerednavigation/general/hide_one_value');

        if ($noneVariant || $oneHiddenVariant) {
            $count = 0;
        } else {
            $count = parent::getItemsCount();
        }

        return $count;
    }

    public function getSettings()
    {
        if (is_null($this->settings)){
            $this->settings = Mage::getResourceModel('brander_layerednavigation/filter')
              ->getFilterByAttributeId($this->getAttributeModel()->getAttributeId()); 
        }
        return $this->settings;
    }
    
    /**
     * Retrieve resource instance
     *
     * @return Brander_LayeredNavigation_Model_Mysql4_Decimal
     */
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getModel('brander_layerednavigation/mysql4_decimal');
        }
        return $this->_resource;
    }

    /**
     * Apply decimal range filter to product collection
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Mage_Catalog_Block_Layer_Filter_Decimal $filterBlock
     * @return Mage_Catalog_Model_Layer_Filter_Decimal
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        if (!$this->calculateRanges()){
            $this->_items = array($this->_createItem('', 0, 0));
        }         

        $filterBlock->setValueFrom(Mage::helper('brander_layerednavigation')->__('From'));
        $filterBlock->setValueTo(Mage::helper('brander_layerednavigation')->__('To'));

        $input = $request->getParam($this->getRequestVar());
        $fromTo = $this->_parseRequestedValue($input);
        if (is_null($fromTo)) {
            return $this;
        }
        list($from, $to) = $fromTo;

        $attributeCode = $this->getAttributeModel()->getAttributeCode();
        /** @var Brander_LayeredNavigation_Helper_Attributes $attributeHelper */
        $attributeHelper = Mage::helper('brander_layerednavigation/attributes');
        if ($attributeHelper->lockApplyFilter($attributeCode, 'attr')) {
            $this->_getResource()->applyFilterToCollection($this, $from, $to);

            $this->getLayer()->getState()->addFilter(
                $this->_createItem($this->_renderItemLabel($from, $to, true), $input)
            );
        }

        $filterBlock->setValueFrom(($from > 0) ? $from : '');
        $filterBlock->setValueTo(($to > 0) ? $to : '');

        if ($this->hideAfterSelection()){
            $this->_items = array();
        }
        elseif ($this->calculateRanges()){
            $this->_items = array($this->_createItem('', 0, 0));
        }

        return $this;
        
    }

    protected function _parseRequestedValue($input)
    {
        if (!$input) {
            return null;
        }

        /* Try $index, $range */
        $inputVals = explode($this->_rangeSeparator, $input);
        if (count($inputVals) == 2) {
            list($index, $range) = $inputVals;
            $from  = ($index-1) * $range;
            $to    = $index * $range;
            return array($from, $to);
        }

        /* Try from to */
        $inputVals = explode($this->_fromToSeparator, $input);
        if (count($inputVals) == 2) {
            list ($from, $to) = $inputVals;
            $from  = floatval($from);
            $to    = floatval($to);
            if ($from < 0.01 && $to < 0.01) {
                return null;
            }
            return array($from, $to);
        }

        return null;
    }

    protected function _renderItemLabel($range, $index, $isFromTo = false)
    {
		if(!$isFromTo) {
			$from  = ($index-1) * $range;
			$to    = $index * $range;
		} else {
			$from = $range;
			$to = $index;
		}
        if ($to > 0) {
            $result = Mage::helper('catalog')->__('%s - %s', $from, $to);
        } else {
            $result = Mage::helper('catalog')->__('%s and above', $from);
        }

        $settings = $this->getSettings();
        if (!empty($settings['value_label'])) {
            $result.= Mage::helper('catalog')->__(' %s', $settings['value_label']);
        }

        return $result;
    }

    public function getRange()
    {
        $settings = $this->getSettings();
        if (!empty($settings['range'])){
            return $settings['range'];
        }
            
        return parent::getRange(); 
    }
    
    public function calculateRanges()
    {
        $settings = $this->getSettings();
        return $settings['display_type'] == Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Price::DT_DEFAULT
        || $settings['display_type'] == Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Price::DT_DROPDOWN;
    } 
    
    public function hideAfterSelection()
    {
        $settings = $this->getSettings();
        if ($settings['from_to_widget']){
            return false;
        }
        if ($settings['display_type'] == Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Price::DT_SLIDER){
            return false;
        }
        return true;
    }
}