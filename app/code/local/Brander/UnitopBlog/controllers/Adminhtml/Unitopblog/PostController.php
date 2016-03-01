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
class Brander_UnitopBlog_Adminhtml_Unitopblog_PostController extends Mage_Adminhtml_Controller_Action
{
    /**
     * constructor - set the used module name
     *
     * @access protected
     * @return void
     * @see Mage_Core_Controller_Varien_Action::_construct()

     */
    protected function _construct()
    {
        $this->setUsedModuleName('Brander_UnitopBlog');
    }

    /**
     * init the post
     *
     * @access protected 
     * @return Brander_UnitopBlog_Model_Post

     */
    protected function _initPost()
    {
        $this->_title($this->__('Blog'))
             ->_title($this->__('Manage Posts'));

        $postId  = (int) $this->getRequest()->getParam('id');
        $post    = Mage::getModel('brander_unitopblog/post')
            ->setStoreId($this->getRequest()->getParam('store', 0));

        if ($postId) {
            $post->load($postId);
        }
        Mage::register('current_post', $post);
        return $post;
    }

    /**
     * default action for post controller
     *
     * @access public
     * @return void

     */
    public function indexAction()
    {
        $this->_title($this->__('Blog'))
             ->_title($this->__('Manage Posts'));
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * new post action
     *
     * @access public
     * @return void

     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * edit post action
     *
     * @access public
     * @return void

     */
    public function editAction()
    {
        $postId  = (int) $this->getRequest()->getParam('id');
        $post    = $this->_initPost();
        if ($postId && !$post->getId()) {
            $this->_getSession()->addError(
                Mage::helper('brander_unitopblog')->__('This post no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        if ($data = Mage::getSingleton('adminhtml/session')->getPostData(true)) {
            $post->setData($data);
        }
        $this->_title($post->getTitle());
        Mage::dispatchEvent(
            'brander_unitopblog_post_edit_action',
            array('post' => $post)
        );
        $this->loadLayout();
        if ($post->getId()) {
            if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
                $switchBlock->setDefaultStoreName(Mage::helper('brander_unitopblog')->__('Default Values'))
                    ->setWebsiteIds($post->getWebsiteIds())
                    ->setSwitchUrl(
                        $this->getUrl(
                            '*/*/*',
                            array(
                                '_current'=>true,
                                'active_tab'=>null,
                                'tab' => null,
                                'store'=>null
                            )
                        )
                    );
            }
        } else {
            $this->getLayout()->getBlock('left')->unsetChild('store_switcher');
        }
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    /**
     * save post action
     *
     * @access public
     * @return void

     */
    public function saveAction()
    {
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $postId   = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);
        $data = $this->getRequest()->getPost();
        if ($data) {
            $post     = $this->_initPost();
            $postData = $this->getRequest()->getPost('post', array());
            $post->addData($postData);
            $post->setAttributeSetId($post->getDefaultAttributeSetId());
                $products = $this->getRequest()->getPost('products', -1);
                if ($products != -1) {
                    $post->setProductsData(
                        Mage::helper('adminhtml/js')->decodeGridSerializedInput($products)
                    );
                }
                $categories = $this->getRequest()->getPost('category_ids', -1);
                if ($categories != -1) {
                    $categories = explode(',', $categories);
                    $categories = array_unique($categories);
                    $post->setCategoriesData($categories);
                }
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $attributeCode) {
                    $post->setData($attributeCode, false);
                }
            }
            try {
                $post->save();
                $postId = $post->getId();
                $this->_getSession()->addSuccess(
                    Mage::helper('brander_unitopblog')->__('Post was saved')
                );
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage())
                    ->setPostData($postData);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError(
                    Mage::helper('brander_unitopblog')->__('Error saving post')
                )
                ->setPostData($postData);
                $redirectBack = true;
            }
        }
        if ($redirectBack) {
            $this->_redirect(
                '*/*/edit',
                array(
                    'id'    => $postId,
                    '_current'=>true
                )
            );
        } else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }

    /**
     * delete post
     *
     * @access public
     * @return void

     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $post = Mage::getModel('brander_unitopblog/post')->load($id);
            try {
                $post->delete();
                $this->_getSession()->addSuccess(
                    Mage::helper('brander_unitopblog')->__('The posts has been deleted.')
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()->setRedirect(
            $this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store')))
        );
    }

    /**
     * mass delete posts
     *
     * @access public
     * @return void

     */
    public function massDeleteAction()
    {
        $postIds = $this->getRequest()->getParam('post');
        if (!is_array($postIds)) {
            $this->_getSession()->addError($this->__('Please select posts.'));
        } else {
            try {
                foreach ($postIds as $postId) {
                    $post = Mage::getSingleton('brander_unitopblog/post')->load($postId);
                    Mage::dispatchEvent(
                        'brander_unitopblog_controller_post_delete',
                        array('post' => $post)
                    );
                    $post->delete();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('brander_unitopblog')->__('Total of %d record(s) have been deleted.', count($postIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass status change - action
     *
     * @access public
     * @return void

     */
    public function massStatusAction()
    {
        $postIds = $this->getRequest()->getParam('post');
        if (!is_array($postIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('brander_unitopblog')->__('Please select posts.')
            );
        } else {
            try {
                foreach ($postIds as $postId) {
                $post = Mage::getSingleton('brander_unitopblog/post')->load($postId)
                    ->setStatus($this->getRequest()->getParam('status'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d posts were successfully updated.', count($postIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('brander_unitopblog')->__('There was an error updating posts.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * grid action
     *
     * @access public
     * @return void

     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * restrict access
     *
     * @access protected
     * @return bool
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()

     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('brander_shop/brander_unitopblog/post');
    }

    /**
     * Export posts in CSV format
     *
     * @access public
     * @return void

     */
    public function exportCsvAction()
    {
        $fileName   = 'posts.csv';
        $content    = $this->getLayout()->createBlock('brander_unitopblog/adminhtml_post_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export posts in Excel format
     *
     * @access public
     * @return void

     */
    public function exportExcelAction()
    {
        $fileName   = 'post.xls';
        $content    = $this->getLayout()->createBlock('brander_unitopblog/adminhtml_post_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export posts in XML format
     *
     * @access public
     * @return void

     */
    public function exportXmlAction()
    {
        $fileName   = 'post.xml';
        $content    = $this->getLayout()->createBlock('brander_unitopblog/adminhtml_post_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * wysiwyg editor action
     *
     * @access public
     * @return void

     */
    public function wysiwygAction()
    {
        $elementId     = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId       = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock(
            'brander_unitopblog/adminhtml_unitopblog_helper_form_wysiwyg_content',
            '',
            array(
                'editor_element_id' => $elementId,
                'store_id'          => $storeId,
                'store_media_url'   => $storeMediaUrl,
            )
        );
        $this->getResponse()->setBody($content->toHtml());
    }

    /**
     * mass Author change
     *
     * @access public
     * @return void

     */
    public function massAuthorAction()
    {
        $postIds = (array)$this->getRequest()->getParam('post');
        $storeId       = (int)$this->getRequest()->getParam('store', 0);
        $flag          = (int)$this->getRequest()->getParam('flag_author');
        if ($flag == 2) {
            $flag = 0;
        }
        try {
            foreach ($postIds as $postId) {
                $post = Mage::getSingleton('brander_unitopblog/post')
                    ->setStoreId($storeId)
                    ->load($postId);
                $post->setAuthor($flag)->save();
            }
            $this->_getSession()->addSuccess(
                Mage::helper('brander_unitopblog')->__('Total of %d record(s) have been updated.', count($postIds))
            );
        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('brander_unitopblog')->__('An error occurred while updating the posts.')
            );
        }
        $this->_redirect('*/*/', array('store'=> $storeId));
    }

    /**
     * mass Show on Homepage change
     *
     * @access public
     * @return void

     */
    public function massShowOnHomepageAction()
    {
        $postIds = (array)$this->getRequest()->getParam('post');
        $storeId       = (int)$this->getRequest()->getParam('store', 0);
        $flag          = (int)$this->getRequest()->getParam('flag_show_on_homepage');
        if ($flag == 2) {
            $flag = 0;
        }
        try {
            foreach ($postIds as $postId) {
                $post = Mage::getSingleton('brander_unitopblog/post')
                    ->setStoreId($storeId)
                    ->load($postId);
                $post->setShowOnHomepage($flag)->save();
            }
            $this->_getSession()->addSuccess(
                Mage::helper('brander_unitopblog')->__('Total of %d record(s) have been updated.', count($postIds))
            );
        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('brander_unitopblog')->__('An error occurred while updating the posts.')
            );
        }
        $this->_redirect('*/*/', array('store'=> $storeId));
    }

    /**
     * mass Archived change
     *
     * @access public
     * @return void

     */
    public function massArchivedAction()
    {
        $postIds = (array)$this->getRequest()->getParam('post');
        $storeId       = (int)$this->getRequest()->getParam('store', 0);
        $flag          = (int)$this->getRequest()->getParam('flag_archived');
        if ($flag == 2) {
            $flag = 0;
        }
        try {
            foreach ($postIds as $postId) {
                $post = Mage::getSingleton('brander_unitopblog/post')
                    ->setStoreId($storeId)
                    ->load($postId);
                $post->setArchived($flag)->save();
            }
            $this->_getSession()->addSuccess(
                Mage::helper('brander_unitopblog')->__('Total of %d record(s) have been updated.', count($postIds))
            );
        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('brander_unitopblog')->__('An error occurred while updating the posts.')
            );
        }
        $this->_redirect('*/*/', array('store'=> $storeId));
    }

    /**
     * mass post category change - action
     *
     * @access public
     * @return void

     */
    public function massPostscategoryIdAction()
    {
        $postIds = $this->getRequest()->getParam('post');
        if (!is_array($postIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('brander_unitopblog')->__('Please select posts.')
            );
        } else {
            try {
                foreach ($postIds as $postId) {
                $post = Mage::getSingleton('brander_unitopblog/post')->load($postId)
                    ->setPostscategoryId($this->getRequest()->getParam('flag_postscategory_id'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d posts were successfully updated.', count($postIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('brander_unitopblog')->__('There was an error updating posts.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * get grid of products action
     *
     * @access public
     * @return void

     */
    public function productsAction()
    {
        $this->_initPost();
        $this->loadLayout();
        $this->getLayout()->getBlock('post.edit.tab.product')
            ->setPostProducts($this->getRequest()->getPost('post_products', null));
        $this->renderLayout();
    }

    /**
     * get grid of products action
     *
     * @access public
     * @return void

     */
    public function productsgridAction()
    {
        $this->_initPost();
        $this->loadLayout();
        $this->getLayout()->getBlock('post.edit.tab.product')
            ->setPostProducts($this->getRequest()->getPost('post_products', null));
        $this->renderLayout();
    }

    /**
     * get categories action
     *
     * @access public
     * @return void

     */
    public function categoriesAction()
    {
        $this->_initPost();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * get child categories action
     *
     * @access public
     * @return void

     */
    public function categoriesJsonAction()
    {
        $this->_initPost();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('brander_unitopblog/adminhtml_post_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
}
