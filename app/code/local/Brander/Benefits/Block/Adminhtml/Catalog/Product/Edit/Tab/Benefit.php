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
class Brander_Benefits_Block_Adminhtml_Catalog_Product_Edit_Tab_Benefit extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     * @access public
     */

    public function __construct()
    {
        parent::__construct();
        $this->setId('benefit_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getProduct()->getId()) {
            $this->setDefaultFilter(array('in_benefits'=>1));
        }
    }

    /**
     * prepare the benefit collection
     *
     * @access protected
     * @return Brander_Benefits_Block_Adminhtml_Catalog_Product_Edit_Tab_Benefit
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('brander_benefits/benefit_collection')->addAttributeToSelect('title');
        if ($this->getProduct()->getId()) {
            $constraint = 'related.product_id='.$this->getProduct()->getId();
        } else {
            $constraint = 'related.product_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('brander_benefits/benefit_product')),
            'related.benefit_id=e.entity_id AND '.$constraint,
            array('position')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * prepare mass action grid
     *
     * @access protected
     * @return Brander_Benefits_Block_Adminhtml_Catalog_Product_Edit_Tab_Benefit
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * prepare the grid columns
     *
     * @access protected
     * @return Brander_Benefits_Block_Adminhtml_Catalog_Product_Edit_Tab_Benefit
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_benefits',
            array(
                'header_css_class'  => 'a-center',
                'type'  => 'checkbox',
                'name'  => 'in_benefits',
                'values'=> $this->_getSelectedBenefits(),
                'align' => 'center',
                'index' => 'entity_id'
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
        $benefits = $this->getProductBenefits();
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
        //used helper here in order not to override the product model
        $selected = Mage::helper('brander_benefits/product')->getSelectedBenefits(Mage::registry('current_product'));
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
            '*/*/benefitsGrid',
            array(
                'id'=>$this->getProduct()->getId()
            )
        );
    }

    /**
     * get the current product
     *
     * @access public
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Add filter
     *
     * @access protected
     * @param object $column
     * @return Brander_Benefits_Block_Adminhtml_Catalog_Product_Edit_Tab_Benefit
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
