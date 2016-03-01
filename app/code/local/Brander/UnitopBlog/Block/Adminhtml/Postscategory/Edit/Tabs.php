<?php
/**
 * Brander UnitopBlog extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        UnitopBlog
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_UnitopBlog_Block_Adminhtml_Postscategory_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public

     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('postscategory_info_tabs');
        $this->setDestElementId('postscategory_tab_content');
        $this->setTitle(Mage::helper('brander_unitopblog')->__('Post Category Information'));
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Postscategory_Edit_Tabs

     */
    protected function _prepareLayout()
    {
        $postscategory = $this->getPostscategory();
        $entity = Mage::getModel('eav/entity_type')
            ->load('brander_unitopblog_postscategory', 'entity_type_code');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($entity->getEntityTypeId());
        $attributes->addFieldToFilter(
            'attribute_code',
            array(
                'nin' => array('meta_title', 'meta_description', 'meta_keywords')
            )
        );
        $attributes->getSelect()->order('additional_table.position', 'ASC');

        $this->addTab(
            'info',
            array(
                'label'   => Mage::helper('brander_unitopblog')->__('Post Category Information'),
                'content' => $this->getLayout()->createBlock(
                    'brander_unitopblog/adminhtml_postscategory_edit_tab_attributes'
                )
                ->setAttributes($attributes)
                ->setAddHiddenFields(true)
                ->toHtml(),
            )
        );
        $seoAttributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entity->getEntityTypeId())
            ->addFieldToFilter(
                'attribute_code',
                array(
                    'in' => array('meta_title', 'meta_description', 'meta_keywords')
                )
            );
        $seoAttributes->getSelect()->order('additional_table.position', 'ASC');

        $this->addTab(
            'meta',
            array(
                'label'   => Mage::helper('brander_unitopblog')->__('Meta'),
                'title'   => Mage::helper('brander_unitopblog')->__('Meta'),
                'content' => $this->getLayout()->createBlock(
                    'brander_unitopblog/adminhtml_postscategory_edit_tab_attributes'
                )
                ->setAttributes($seoAttributes)
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve post category entity
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Postscategory

     */
    public function getPostscategory()
    {
        return Mage::registry('current_postscategory');
    }
}
