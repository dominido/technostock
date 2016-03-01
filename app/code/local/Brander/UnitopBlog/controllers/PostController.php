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
class Brander_UnitopBlog_PostController extends Mage_Core_Controller_Front_Action
{

    /**
      * default action
      *
      * @access public
      * @return void

      */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if (Mage::helper('brander_unitopblog/post')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('brander_unitopblog')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'posts',
                    array(
                        'label' => Mage::helper('brander_unitopblog')->__('Posts'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('brander_unitopblog/post')->getPostsUrl());
        }
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('brander_unitopblog/post/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('brander_unitopblog/post/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('brander_unitopblog/post/meta_description'));
        }
        $this->renderLayout();
    }

    /**
     * init Post
     *
     * @access protected
     * @return Brander_UnitopBlog_Model_Post

     */
    protected function _initPost()
    {
        $postId   = $this->getRequest()->getParam('id', 0);
        $post     = Mage::getModel('brander_unitopblog/post')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($postId);
        if (!$post->getId()) {
            return false;
        } elseif (!$post->getStatus()) {
            return false;
        }
        return $post;
    }

    /**
     * view post action
     *
     * @access public
     * @return void

     */
    public function viewAction()
    {
        $post = $this->_initPost();
        if (!$post) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_post', $post);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('unitopblog-post unitopblog-post' . $post->getId());
        }
        if (Mage::helper('brander_unitopblog/post')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('brander_unitopblog')->__('Homepage'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'posts',
                    array(
                        'label' => Mage::helper('brander_unitopblog/postscategory')->getBlogPageTitle(),
                        'link'  => Mage::helper('brander_unitopblog/postscategory')->getPostscategoriesUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'post',
                    array(
                        'label' => $post->getTitle(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $post->getPostUrl());
        }
        if ($headBlock) {
            if ($post->getMetaTitle()) {
                $headBlock->setTitle($post->getMetaTitle());
            } else {
                $headBlock->setTitle($post->getTitle());
            }
            $headBlock->setKeywords($post->getMetaKeywords());
            $headBlock->setDescription($post->getMetaDescription());
        }
        $this->renderLayout();
    }

    /**
     * posts rss list action
     *
     * @access public
     * @return void

     */
    public function rssAction()
    {
        if (Mage::helper('brander_unitopblog/post')->isRssEnabled()) {
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            $this->loadLayout(false);
            $this->renderLayout();
        } else {
            $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
            $this->getResponse()->setHeader('Status', '404 File not found');
            $this->_forward('nofeed', 'index', 'rss');
        }
    }

    /**
     * Submit new comment action
     * @access public

     */
    public function commentpostAction()
    {
        $data   = $this->getRequest()->getPost();
        $post = $this->_initPost();
        $session    = Mage::getSingleton('core/session');
        if ($post) {
            if ($post->getAllowComments()) {
                if ((Mage::getSingleton('customer/session')->isLoggedIn() ||
                    Mage::getStoreConfigFlag('brander_unitopblog/post/allow_guest_comment'))) {
                    $comment  = Mage::getModel('brander_unitopblog/post_comment')->setData($data);
                    $validate = $comment->validate();
                    if ($validate === true) {
                        try {
                            $comment->setPostId($post->getId())
                                ->setStatus(Brander_UnitopBlog_Model_Post_Comment::STATUS_PENDING)
                                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                                ->setStores(array(Mage::app()->getStore()->getId()))
                                ->save();
                            $session->addSuccess($this->__('Your comment has been accepted for moderation.'));
                        } catch (Exception $e) {
                            $session->setPostCommentData($data);
                            $session->addError($this->__('Unable to post the comment.'));
                        }
                    } else {
                        $session->setPostCommentData($data);
                        if (is_array($validate)) {
                            foreach ($validate as $errorMessage) {
                                $session->addError($errorMessage);
                            }
                        } else {
                            $session->addError($this->__('Unable to post the comment.'));
                        }
                    }
                } else {
                    $session->addError($this->__('Guest comments are not allowed'));
                }
            } else {
                $session->addError($this->__('This post does not allow comments'));
            }
        }
        $this->_redirectReferer();
    }
}
