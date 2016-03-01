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
class Brander_UnitopBlog_Block_Postscategory_Widget_Subtree extends Brander_UnitopBlog_Block_Postscategory_List implements
    Mage_Widget_Block_Interface
{
    protected $_template = 'brander_unitopblog/postscategory/widget/subtree.phtml';
    /**
     * prepare the layout
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Postscategory_Widget_Subtree

     */
    protected function _prepareLayout()
    {
        $this->getPostscategories()->addFieldToFilter('entity_id', $this->getPostscategoryId());
        return $this;
    }

    /**
     * get the display mode
     *
     * @access protected
     * @return int

     */
    protected function _getDisplayMode()
    {
        return 1;
    }

    /**
     * get the element id
     *
     * @access protected
     * @return int

     */
    public function getUniqueId()
    {
        if (!$this->getData('uniq_id')) {
            $this->setData('uniq_id', uniqid('subtree'));
        }
        return $this->getData('uniq_id');
    }
}
