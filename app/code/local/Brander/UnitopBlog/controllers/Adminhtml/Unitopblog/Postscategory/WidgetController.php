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
class Brander_UnitopBlog_Adminhtml_Unitopblog_Postscategory_WidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Chooser Source action
     *
     * @access public
     * @return void

     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $grid = $this->getLayout()->createBlock(
            'brander_unitopblog/adminhtml_postscategory_widget_chooser',
            '',
            array(
                'id' => $uniqId,
            )
        );
        $this->getResponse()->setBody($grid->toHtml());
    }

    /**
     * post categories json action
     *
     * @access public
     * @return void

     */
    public function postscategoriesJsonAction()
    {
        if ($postscategoryId = (int) $this->getRequest()->getPost('id')) {
            $postscategory = Mage::getModel('brander_unitopblog/postscategory')->load($postscategoryId);
            if ($postscategory->getId()) {
                Mage::register('postscategory', $postscategory);
                Mage::register('current_postscategory', $postscategory);
            }
            $this->getResponse()->setBody(
                $this->_getPostscategoryTreeBlock()->getTreeJson($postscategory)
            );
        }
    }

    /**
     * get post category tree block
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Postscategory_Widget_Chooser

     */
    protected function _getPostscategoryTreeBlock()
    {
        return $this->getLayout()->createBlock(
            'brander_unitopblog/adminhtml_postscategory_widget_chooser',
            '',
            array(
                'id' => $this->getRequest()->getParam('uniq_id'),
                'use_massaction' => $this->getRequest()->getParam('use_massaction', false)
            )
        );
    }
}
