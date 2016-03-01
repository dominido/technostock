<?php

class Brander_LayeredNavigation_Model_Mysql4_Decimal extends Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Decimal
{
    protected $_minMax = null;

    /**
     * @param Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Decimal $filter
     * @param float $from
     * @param float $to
     * @return Mage_Catalog_Model_Resource_Layer_Filter_Decimal
     */
    public function applyFilterToCollection($filter, $from, $to)
    {
        $this->_applyFilterToCollectionDb($filter, $from, $to);
    }

    /**
     * @param Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Decimal $filter
     * @param float $from
     * @param float $to
     */
    protected function _applyFilterToCollectionDb($filter, $from, $to)
    {
        $collection = $filter->getLayer()->getProductCollection();
        $attribute  = $filter->getAttributeModel();

        $connection = $this->_getReadAdapter();

        $tableAlias = sprintf('%s_idx', $attribute->getAttributeCode());

        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId()),
        );

        $collection->getSelect()->join(
            array($tableAlias => $this->getMainTable()),
            implode(' AND ', $conditions),
            array()
        );

        // bundle items has 2 records if single item has special price
        if (Mage::getStoreConfig('brander_layerednavigation/general/bundle')){
            $collection->getSelect()->distinct(true);
        }

        list($min, $max) = $this->getMinMax($filter);

		$settings = $filter->getSettings();
		$isSlider = (isset($settings['display_type']) && $settings['display_type'] == Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Price::DT_SLIDER);

        $toSign = ($max == $to || $isSlider || $from == $to) ? '<=' : '<';
        $collection->getSelect()->where("{$tableAlias}.value >= ?", $from);

        if ($to >= $min) {
            $collection->getSelect()->where("{$tableAlias}.value {$toSign} ?", $to);
        }
    }

    /**
     * @param Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Decimal $filter
     * @return array (max, min)
     */
    public function getMinMax($filter)
    {
        if (is_null($this->_minMax)) {
            $this->_minMax = parent::getMinMax($filter);
        }
        return $this->_minMax;
    }

    /**
     * Retrieve clean select with joined index table
     * Joined table has index
     *
     * @param Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Decimal $filter
     * @return Varien_Db_Select
     */
    protected function _getSelect($filter)
    {
        /** @var Enterprise_Search_Model_Resource_Collection $collection */
        $collection = $filter->getLayer()->getProductCollection();

        // clone select from collection with filters
        $select = clone $collection->getSelect();
        // reset columns, order and limitation conditions
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        
        $attributeId = $filter->getAttributeModel()->getId();
        $storeId     = $collection->getStoreId();

        $select->join(
            array('decimal_index' => $this->getMainTable()),
            'e.entity_id = decimal_index.entity_id'.
            ' AND ' . $this->_getReadAdapter()->quoteInto('decimal_index.attribute_id = ?', $attributeId) .
            ' AND ' . $this->_getReadAdapter()->quoteInto('decimal_index.store_id = ?', $storeId),
            array()
        );
        
        $code = $filter->getAttributeModel()->getAttributeCode();
        
        $field = $code . "_idx.value";
        
        /*
         * Reset where condition of current filter
         */
        $oldWhere = $select->getPart(Varien_Db_Select::WHERE);
        $newWhere = array();
        
        foreach ($oldWhere as $cond) {
            if (false === strpos($cond, $field)) {
                $newWhere[] = $cond;
            }
        }
        if ($newWhere && substr($newWhere[0], 0, 3) == 'AND') {
            $newWhere[0] = substr($newWhere[0], 3); 
        }
                      
        $select->setPart(Varien_Db_Select::WHERE, $newWhere); 
        return $select;
    }
}