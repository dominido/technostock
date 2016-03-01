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
class Brander_ProductBanners_Block_Adminhtml_Banner_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public

     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('banner_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('brander_productbanners')->__('Banner Information'));
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Brander_ProductBanners_Block_Adminhtml_Banner_Edit_Tabs

     */
    protected function _prepareLayout()
    {
        $banner = $this->getBanner();
        $entity = Mage::getModel('eav/entity_type')
            ->load('brander_productbanners_banner', 'entity_type_code');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($entity->getEntityTypeId());
        $attributes->getSelect()->order('additional_table.position', 'ASC');

        $this->addTab(
            'info',
            array(
                'label'   => Mage::helper('brander_productbanners')->__('Banner Information'),
                'content' => $this->getLayout()->createBlock(
                    'brander_productbanners/adminhtml_banner_edit_tab_attributes'
                )
                ->setAttributes($attributes)
                ->toHtml(),
            )
        );
        $this->addTab(
            'products',
            array(
                'label' => Mage::helper('brander_productbanners')->__('Associated products'),
                'url'   => $this->getUrl('*/*/products', array('_current' => true)),
                'class' => 'ajax'
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve banner entity
     *
     * @access public
     * @return Brander_ProductBanners_Model_Banner

     */
    public function getBanner()
    {
        return Mage::registry('current_banner');
    }
}
