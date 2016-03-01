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
class Brander_UnitopBlog_Block_Postscategory_Children extends Brander_UnitopBlog_Block_Postscategory_List
{
    /**
     * prepare the layout
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Postscategory_Children

     */
    protected function _prepareLayout()
    {
        $this->getPostscategories()->addFieldToFilter('parent_id', $this->getCurrentPostscategory()->getId());
        return $this;
    }

    /**
     * get the current post category
     *
     * @access protected
     * @return Brander_UnitopBlog_Model_Postscategory

     */
    public function getCurrentPostscategory()
    {
        return Mage::registry('current_postscategory');
    }
}
