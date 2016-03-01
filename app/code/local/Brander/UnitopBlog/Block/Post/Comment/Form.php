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
class Brander_UnitopBlog_Block_Post_Comment_Form extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public

     */
    public function __construct()
    {
        $customerSession = Mage::getSingleton('customer/session');
        parent::__construct();
        $data =  Mage::getSingleton('customer/session')->getPostCommentFormData(true);
        $data = new Varien_Object($data);
        // add logged in customer name as nickname
        if (!$data->getName()) {
            $customer = $customerSession->getCustomer();
            if ($customer && $customer->getId()) {
                $data->setName($customer->getFirstname());
                $data->setEmail($customer->getEmail());
            }
        }
        $this->setAllowWriteCommentFlag(
            $customerSession->isLoggedIn() ||
            Mage::getStoreConfigFlag('brander_unitopblog/post/allow_guest_comment')
        );
        if (!$this->getAllowWriteCommentFlag()) {
            $this->setLoginLink(
                Mage::getUrl(
                    'customer/account/login/',
                    array(
                        Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME => Mage::helper('core')->urlEncode(
                            Mage::getUrl('*/*/*', array('_current' => true)) .
                            '#comment-form'
                        )
                    )
                )
            );
        }
        $this->setCommentData($data);
    }

    /**
     * get current post
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Post

     */
    public function getPost()
    {
        return Mage::registry('current_post');
    }

    /**
     * get form action
     *
     * @access public
     * @return string

     */
    public function getAction()
    {
        return Mage::getUrl(
            'brander_unitopblog/post/commentpost',
            array('id' => $this->getPost()->getId())
        );
    }

    public function getPostTitle()
    {
        return $this->getPost()->getTitle();
    }

    public function checkNecessarilyEmail()
    {
        return Mage::helper('brander_unitopblog')->getCfg('brander_unitopblog/post/necessarily_poster_email');
    }
}
