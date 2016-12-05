<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Category Image admin edit tabs
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
class Brander_CategoryBanner_Block_Adminhtml_Categorybanner_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('categorybanner_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('brander_categorybanner')->__('Category Image'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Brander_CategoryBanner_Block_Adminhtml_Categorybanner_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_categorybanner',
            array(
                'label'   => Mage::helper('brander_categorybanner')->__('Category Image'),
                'title'   => Mage::helper('brander_categorybanner')->__('Category Image'),
                'content' => $this->getLayout()->createBlock(
                    'brander_categorybanner/adminhtml_categorybanner_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_categorybanner',
                array(
                    'label'   => Mage::helper('brander_categorybanner')->__('Store views'),
                    'title'   => Mage::helper('brander_categorybanner')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'brander_categorybanner/adminhtml_categorybanner_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        $this->addTab(
            'categories',
            array(
                'label' => Mage::helper('brander_categorybanner')->__('Associated categories'),
                'url'   => $this->getUrl('*/*/categories', array('_current' => true)),
                'class' => 'ajax'
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve category image entity
     *
     * @access public
     * @return Brander_CategoryBanner_Model_Categorybanner
     * @author Ultimate Module Creator
     */
    public function getCategorybanner()
    {
        return Mage::registry('current_categorybanner');
    }
}
