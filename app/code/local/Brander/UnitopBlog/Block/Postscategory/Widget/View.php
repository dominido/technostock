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
class Brander_UnitopBlog_Block_Postscategory_Widget_View extends Mage_Core_Block_Template implements
    Mage_Widget_Block_Interface
{
    protected $_htmlTemplate = 'brander_unitopblog/postscategory/widget/view.phtml';

    /**
     * Prepare a for widget
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Postscategory_Widget_View

     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $postscategoryId = $this->getData('postscategory_id');
        if ($postscategoryId) {
            $postscategory = Mage::getModel('brander_unitopblog/postscategory')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($postscategoryId);
            if ($postscategory->getStatusPath()) {
                $this->setCurrentPostscategory($postscategory);
                $this->setTemplate($this->_htmlTemplate);
            }
        }
        return $this;
    }
}
