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
class Brander_ProductBanners_Block_Adminhtml_Banner_Attribute_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * constructor
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('banner_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('brander_productbanners')->__('Attribute Information'));
    }

    /**
     * add attribute tabs
     *
     * @access protected
     * @return Brander_ProductBanners_Adminhtml_Banner_Attribute_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'main',
            array(
                'label'     => Mage::helper('brander_productbanners')->__('Properties'),
                'title'     => Mage::helper('brander_productbanners')->__('Properties'),
                'content'   => $this->getLayout()->createBlock(
                    'brander_productbanners/adminhtml_banner_attribute_edit_tab_main'
                )
                ->toHtml(),
                'active'    => true
            )
        );
        $this->addTab(
            'labels',
            array(
                'label'     => Mage::helper('brander_productbanners')->__('Manage Label / Options'),
                'title'     => Mage::helper('brander_productbanners')->__('Manage Label / Options'),
                'content'   => $this->getLayout()->createBlock(
                    'brander_productbanners/adminhtml_banner_attribute_edit_tab_options'
                )
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }
}
