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
 * Shop Review model
 *
 * @category    Brander
 * @package     Brander_ShopReview
 * @author      Ultimate Module Creator
 */
class Brander_ShopReview_Model_Shopreview
    extends Mage_Core_Model_Abstract {
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'brander_shopreview_shopreview';
    const CACHE_TAG = 'brander_shopreview_shopreview';
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'brander_shopreview_shopreview';

    /**
     * Parameter name in event
     * @var string
     */
    protected $_eventObject = 'shopreview';
    /**
     * constructor
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function _construct(){
        parent::_construct();
        $this->_init('brander_shopreview/shopreview');
    }
    /**
     * before save shop review
     * @access protected
     * @return Brander_ShopReview_Model_Shopreview
     * @author Ultimate Module Creator
     */
    protected function _beforeSave(){
        parent::_beforeSave();
        $now = date("Y/m/d H:i:s", Mage::getModel('core/date')->timestamp(time()));
        if ($this->isObjectNew()){
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }
    /**
     * save shopreview relation
     * @access public
     * @return Brander_ShopReview_Model_Shopreview
     * @author Ultimate Module Creator
     */
    protected function _afterSave() {
        return parent::_afterSave();
    }
    /**
     * get default values
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getDefaultValues() {
        $values = array();
        $values['status'] = 1;
        $values['review_status'] = 0;

        return $values;
    }
}
