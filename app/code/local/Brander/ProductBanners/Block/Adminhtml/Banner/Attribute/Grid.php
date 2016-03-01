<?php
/**
 * Brander ProductBanners extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        ProductBanners
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_ProductBanners_Block_Adminhtml_Banner_Attribute_Grid extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    /**
     * Prepare banner attributes grid collection object
     *
     * @access protected
     * @return Brander_ProductBanners_Block_Adminhtml_Banner_Attribute_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('brander_productbanners/banner_attribute_collection')
            ->addVisibleFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare banner attributes grid columns
     *
     * @access protected
     * @return Brander_ProductBanners_Block_Adminhtml_Banner_Attribute_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $this->addColumnAfter(
            'is_global',
            array(
                'header'   => Mage::helper('brander_productbanners')->__('Scope'),
                'sortable' => true,
                'index'    => 'is_global',
                'type'     => 'options',
                'options'  => array(
                    Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE   =>
                        Mage::helper('brander_productbanners')->__('Store View'),
                    Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>
                        Mage::helper('brander_productbanners')->__('Website'),
                    Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL  =>
                        Mage::helper('brander_productbanners')->__('Global'),
                ),
                'align' => 'center',
            ),
            'is_user_defined'
        );
        return $this;
    }
}
