<?php
/**
 * Brander_ShopReview extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Brander
 * @package		Brander_ShopReview
 * @copyright  	Copyright (c) 2016
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Shop Review admin edit tabs
 *
 * @category    Brander
 * @package     Brander_ShopReview
 * @author      Ultimate Module Creator
 */
class Brander_ShopReview_Block_Adminhtml_Shopreview_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs {
    /**
     * Initialize Tabs
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct() {
        parent::__construct();
        $this->setId('shopreview_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('brander_shopreview')->__('Shop Review'));
    }
    /**
     * before render html
     * @access protected
     * @return Brander_ShopReview_Block_Adminhtml_Shopreview_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml(){
        $this->addTab('form_shopreview', array(
            'label'        => Mage::helper('brander_shopreview')->__('Shop Review'),
            'title'        => Mage::helper('brander_shopreview')->__('Shop Review'),
            'content'     => $this->getLayout()->createBlock('brander_shopreview/adminhtml_shopreview_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
    /**
     * Retrieve shop review entity
     * @access public
     * @return Brander_ShopReview_Model_Shopreview
     * @author Ultimate Module Creator
     */
    public function getShopreview(){
        return Mage::registry('current_shopreview');
    }
}
