<?php
/**
 * Brander Benefits extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        Benefits
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_Benefits_Block_Adminhtml_Catalog_Category_Tab_Benefit extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('catalog_category_benefit');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(array('in_benefits'=>1));
        }
    }

    /**
     * get current category
     *
     * @access public
     * @return Mage_Catalog_Model_Category|null
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * prepare the collection
     *
     * @access protected
     * @return Brander_Benefits_Block_Adminhtml_Catalog_Category_Tab_Benefit
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('brander_benefits/benefit_collection')->addAttributeToSelect('title');
        if ($this->getCategory()->getId()) {
            $constraint = 'related.category_id='.$this->getCategory()->getId();
        } else {
            $constraint = 'related.category_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('brander_benefits/benefit_category')),
            'related.benefit_id=e.entity_id AND '.$constraint,
            array('position')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare the columns
     *
     * @access protected
     * @return Brander_Benefits_Block_Adminhtml_Catalog_Category_Tab_Benefit
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_benefits',
            array(
                'header_css_class'  => 'a-center',
                'type'   => 'checkbox',
                'name'   => 'in_benefits',
                'values' => $this->_getSelectedBenefits(),
                'align'  => 'center',
                'index'  => 'entity_id'
            )
        );
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('brander_benefits')->__('Id'),
                'type'   => 'number',
                'align'  => 'left',
                'index'  => 'entity_id',
            )
        );
        $this->addColumn(
            'title',
            array(
                'header' => Mage::helper('brander_benefits')->__('Title'),
                'align'  => 'left',
                'index'  => 'title',
                'renderer' => 'brander_benefits/adminhtml_helper_column_renderer_relation',
                'params' => array(
                    'id' => 'getId'
                ),
                'base_link' => 'adminhtml/benefits_benefit/edit',
            )
        );
        $this->addColumn(
            'position',
            array(
                'header'         => Mage::helper('brander_benefits')->__('Position'),
                'name'           => 'position',
                'width'          => 60,
                'type'           => 'number',
                'validate_class' => 'validate-number',
                'index'          => 'position',
                'editable'       => true,
            )
        );
        return parent::_prepareColumns();
    }

    /**
     * Retrieve selected benefits
     *
     * @access protected
     * @return array
     */
    protected function _getSelectedBenefits()
    {
        $benefits = $this->getCategoryBenefits();
        if (!is_array($benefits)) {
            $benefits = array_keys($this->getSelectedBenefits());
        }
        return $benefits;
    }

    /**
     * Retrieve selected benefits
     *
     * @access protected
     * @return array
     */
    public function getSelectedBenefits()
    {
        $benefits = array();
        //used helper here in order not to override the category model
        $selected = Mage::helper('brander_benefits/category')->getSelectedBenefits(Mage::registry('current_category'));
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $benefit) {
            $benefits[$benefit->getId()] = array('position' => $benefit->getPosition());
        }
        return $benefits;
    }

    /**
     * get row url
     *
     * @access public
     * @param Brander_Benefits_Model_Benefit
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * get grid url
     *
     * @access public
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'adminhtml/benefits_benefit_catalog_category/benefitsgrid',
            array(
                'id'=>$this->getCategory()->getId()
            )
        );
    }

    /**
     * Add filter
     *
     * @access protected
     * @param object $column
     * @return Brander_Benefits_Block_Adminhtml_Catalog_Category_Tab_Benefit
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_benefits') {
            $benefitIds = $this->_getSelectedBenefits();
            if (empty($benefitIds)) {
                $benefitIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$benefitIds));
            } else {
                if ($benefitIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$benefitIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
