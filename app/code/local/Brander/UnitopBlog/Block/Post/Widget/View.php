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
class Brander_UnitopBlog_Block_Post_Widget_View extends Mage_Core_Block_Template implements
    Mage_Widget_Block_Interface
{
    protected $_htmlTemplate = 'brander_unitopblog/post/widget/view.phtml';

    /**
     * Prepare a for widget
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Post_Widget_View

     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $postId = $this->getData('post_id');
        if ($postId) {
            $post = Mage::getModel('brander_unitopblog/post')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($postId);
            if ($post->getStatus()) {
                $this->setCurrentPost($post);
                $this->setTemplate($this->_htmlTemplate);
            }
        }
        return $this;
    }
}
