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
 * Shop Review helper
 *
 * @category    Brander
 * @package     Brander_ShopReview
 * @author      Ultimate Module Creator
 */
class Brander_ShopReview_Helper_Shopreview
    extends Mage_Core_Helper_Abstract {
    /**
     * get the url to the shop review list page
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getShopreviewsUrl(){
        if ($listKey = Mage::getStoreConfig('brander_shopreview/shopreview/url_rewrite_list')) {
            return Mage::getUrl('', array('_direct'=>$listKey));
        }
        return Mage::getUrl('brander_shopreview/index/index');
    }
    /**
     * check if breadcrumbs can be used
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function getUseBreadcrumbs(){
        return Mage::getStoreConfigFlag('brander_shopreview/shopreview/breadcrumbs');
    }

    public function getPostReviewsUrl(){
        return Mage::getUrl('brander_shopreview/index/post');
    }
}
