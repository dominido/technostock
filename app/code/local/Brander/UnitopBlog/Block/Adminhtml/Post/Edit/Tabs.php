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
class Brander_UnitopBlog_Block_Adminhtml_Post_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public

     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('post_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('brander_unitopblog')->__('Post Information'));
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Post_Edit_Tabs

     */
    protected function _prepareLayout()
    {
        $post = $this->getPost();
        $entity = Mage::getModel('eav/entity_type')
            ->load('brander_unitopblog_post', 'entity_type_code');
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
                'label'   => Mage::helper('brander_unitopblog')->__('Post Information'),
                'content' => $this->getLayout()->createBlock(
                    'brander_unitopblog/adminhtml_post_edit_tab_attributes'
                )
                ->setAttributes($attributes)
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
                    'brander_unitopblog/adminhtml_post_edit_tab_attributes'
                )
                ->setAttributes($seoAttributes)
                ->toHtml(),
            )
        );
        $this->addTab(
            'products',
            array(
                'label' => Mage::helper('brander_unitopblog')->__('Associated products'),
                'url'   => $this->getUrl('*/*/products', array('_current' => true)),
                'class' => 'ajax'
            )
        );
        $this->addTab(
            'categories',
            array(
                'label' => Mage::helper('brander_unitopblog')->__('Associated categories'),
                'url'   => $this->getUrl('*/*/categories', array('_current' => true)),
                'class' => 'ajax'
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve post entity
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Post

     */
    public function getPost()
    {
        return Mage::registry('current_post');
    }
}
