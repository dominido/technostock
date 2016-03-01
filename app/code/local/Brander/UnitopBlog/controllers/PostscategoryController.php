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
class Brander_UnitopBlog_PostscategoryController extends Mage_Core_Controller_Front_Action
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

        $postCategoryHelper = Mage::helper('brander_unitopblog/postscategory');
        if (Mage::helper('brander_unitopblog/postscategory')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('brander_unitopblog')->__('Homepage'),
                        'link'  => Mage::getUrl(),
                    )
                );

                if ($postCategoryHelper->isBlogSubcategory()) {
                    $breadcrumbBlock->addCrumb(
                        'postscategories',
                        array(
                            'label' => $postCategoryHelper->getBlogCrumb(),
                            'link'  => $postCategoryHelper->getPostscategoriesUrl(),
                        )
                    );

                    $breadcrumbBlock->addCrumb(
                        'postscategory',
                        array(
                            'label' => $postCategoryHelper->getActiveCategoryTitle(),
                            'link'  => '',
                        )
                    );
                } else {
                    $breadcrumbBlock->addCrumb(
                        'postscategories',
                        array(
                            'label' => $postCategoryHelper->getBlogCrumb(),
                            'link'  => '',
                        )
                    );
                }
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('brander_unitopblog/postscategory')->getPostscategoriesUrl());
        }
        if ($headBlock) {

            $headBlock->setTitle($this->getCategoryTitle());
            $headBlock->setKeywords(Mage::getStoreConfig('brander_unitopblog/postscategory/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('brander_unitopblog/postscategory/meta_description'));
        }
        $this->renderLayout();
    }

    protected function getCategoryTitle()
    {
        $blogTitle = Mage::getStoreConfig('brander_unitopblog/postscategory/meta_title');
        if ($title = Mage::helper('brander_unitopblog/postscategory')->getActiveCategoryTitle()) {
            $blogTitle = $blogTitle . ' / ' . $title;
        }
        return $blogTitle;
    }


    /**
     * init Post Category
     *
     * @access protected
     * @return Brander_UnitopBlog_Model_Postscategory

     */
    protected function _initPostscategory()
    {
        $postscategoryId   = $this->getRequest()->getParam('id', 0);
        $postscategory     = Mage::getModel('brander_unitopblog/postscategory')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($postscategoryId);
        if (!$postscategory->getId()) {
            return false;
        } elseif (!$postscategory->getStatus()) {
            return false;
        }
        return $postscategory;
    }

    /**
     * view post category action
     *
     * @access public
     * @return void

     */
    public function viewAction()
    {
        $postscategory = $this->_initPostscategory();
        if (!$postscategory) {
            $this->_forward('no-route');
            return;
        }
        if (!$postscategory->getStatusPath()) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_postscategory', $postscategory);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('unitopblog-postscategory unitopblog-postscategory' . $postscategory->getId());
        }
        if (Mage::helper('brander_unitopblog/postscategory')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('brander_unitopblog')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'postscategories',
                    array(
                        'label' => Mage::helper('brander_unitopblog')->__('Post Categories'),
                        'link'  => Mage::helper('brander_unitopblog/postscategory')->getPostscategoriesSeoUrl(),
                    )
                );
                $parents = $postscategory->getParentPostscategories();
                foreach ($parents as $parent) {
                    if ($parent->getId() != Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId() &&
                        $parent->getId() != $postscategory->getId()) {
                        $breadcrumbBlock->addCrumb(
                            'postscategory-'.$parent->getId(),
                            array(
                                'label'    => $parent->getTitle(),
                                'link'    => $link = $parent->getPostscategoryUrl(),
                            )
                        );
                    }
                }
                $breadcrumbBlock->addCrumb(
                    'postscategory',
                    array(
                        'label' => $postscategory->getTitle(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $postscategory->getPostscategoryUrl());
        }
        if ($headBlock) {
            if ($postscategory->getMetaTitle()) {
                $headBlock->setTitle($postscategory->getMetaTitle());
            } else {
                $headBlock->setTitle($postscategory->getTitle());
            }
            $headBlock->setKeywords($postscategory->getMetaKeywords());
            $headBlock->setDescription($postscategory->getMetaDescription());
        }
        $this->renderLayout();
    }
}
