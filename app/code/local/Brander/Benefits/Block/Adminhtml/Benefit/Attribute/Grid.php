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
class Brander_Benefits_Block_Adminhtml_Benefit_Attribute_Grid extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    /**
     * Prepare benefit attributes grid collection object
     *
     * @access protected
     * @return Brander_Benefits_Block_Adminhtml_Benefit_Attribute_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('brander_benefits/benefit_attribute_collection')
            ->addVisibleFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare benefit attributes grid columns
     *
     * @access protected
     * @return Brander_Benefits_Block_Adminhtml_Benefit_Attribute_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $this->addColumnAfter(
            'is_global',
            array(
                'header'   => Mage::helper('brander_benefits')->__('Scope'),
                'sortable' => true,
                'index'    => 'is_global',
                'type'     => 'options',
                'options'  => array(
                    Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE   =>
                        Mage::helper('brander_benefits')->__('Store View'),
                    Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>
                        Mage::helper('brander_benefits')->__('Website'),
                    Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL  =>
                        Mage::helper('brander_benefits')->__('Global'),
                ),
                'align' => 'center',
            ),
            'is_user_defined'
        );
        return $this;
    }
}
