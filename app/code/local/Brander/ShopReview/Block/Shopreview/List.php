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
 * Shop Review list block
 *
 * @category    Brander
 * @package     Brander_ShopReview
 * @author Ultimate Module Creator
 */
class Brander_ShopReview_Block_Shopreview_List
    extends Mage_Core_Block_Template {
    /**
     * initialize
     * @access public
     * @author Ultimate Module Creator
     */
     public function __construct(){
        parent::__construct();
         $shopreviews = Mage::getResourceModel('brander_shopreview/shopreview_collection')
                         ->addFieldToFilter('review_status', 2);
        $shopreviews->setOrder('entity_id', 'desc');
        $this->setShopreviews($shopreviews);
    }
    /**
     * prepare the layout
     * @access protected
     * @return Brander_ShopReview_Block_Shopreview_List
     * @author Ultimate Module Creator
     */
    /*protected function _prepareLayout(){
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'brander_shopreview.shopreview.html.pager')
            ->setCollection($this->getShopreviews());
        $this->setChild('pager', $pager);
        $this->getShopreviews()->load();
        return $this;
    }*/
    /**
     * get the pager html
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    /*public function getPagerHtml(){
        return $this->getChildHtml('pager');
    }*/
}
