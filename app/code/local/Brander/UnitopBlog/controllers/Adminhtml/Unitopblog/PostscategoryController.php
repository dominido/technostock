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
class Brander_UnitopBlog_Adminhtml_UnitopBlog_PostscategoryController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize requested post category and put it into registry.
     * Root post category can be returned, if inappropriate store/post category is specified
     *
     * @access protected
     * @param bool $getRootInstead
     * @return Brander_UnitopBlog_Model_Postscategory

     */
    protected function _initPostscategory($getRootInstead = false)
    {
        $this->_title($this->__('Blog'))
             ->_title($this->__('Manage Post Categories'));
        $postscategoryId = (int) $this->getRequest()->getParam('id', false);
        $storeId    = (int) $this->getRequest()->getParam('store');
        $postscategory = Mage::getModel('brander_unitopblog/postscategory');
        $postscategory->setStoreId($storeId);

        if ($postscategoryId) {
            $postscategory->load($postscategoryId);
            if ($storeId) {
                $rootId = Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId();
                if (!in_array($rootId, $postscategory->getPathIds())) {
                    // load root post category instead wrong one
                    if ($getRootInstead) {
                        $postscategory->load($rootId);
                    } else {
                        $this->_redirect('*/*/', array('_current'=>true, 'id'=>null));
                        return false;
                    }
                }
            }
        }

        if ($activeTabId = (string) $this->getRequest()->getParam('active_tab_id')) {
            Mage::getSingleton('admin/session')->setPostscategoryActiveTabId($activeTabId);
        }

        Mage::register('postscategory', $postscategory);
        Mage::register('current_postscategory', $postscategory);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $postscategory;
    }

    /**
     * index action
     *
     * @access public

     */
    public function indexAction()
    {
        $this->_forward('edit');
    }

    /**
     * Add new post category form
     *
     * @access public

     */
    public function addAction()
    {
        Mage::getSingleton('admin/session')->unsPostscategoryActiveTabId();
        $this->_forward('edit');
    }

    /**
     * Edit post category page
     *
     * @access public

     */
    public function editAction()
    {
        $params['_current'] = true;
        $redirect = false;

        $storeId = (int) $this->getRequest()->getParam('store');
        $parentId = (int) $this->getRequest()->getParam('parent');
        $_prevStoreId = Mage::getSingleton('admin/session')
            ->getPostscategoryLastViewedStore(true);

        if (!empty($_prevStoreId) && !$this->getRequest()->getQuery('isAjax')) {
            $params['store'] = $_prevStoreId;
            $redirect = true;
        }

        $postscategoryId = (int) $this->getRequest()->getParam('id');
        $_prevPostscategoryId = Mage::getSingleton('admin/session')
            ->getLastEditedPostscategory(true);


        if ($_prevPostscategoryId
            && !$this->getRequest()->getQuery('isAjax')
            && !$this->getRequest()->getParam('clear')) {
             $this->getRequest()->setParam('id', $_prevPostscategoryId);
        }

        if ($redirect) {
            $this->_redirect('*/*/edit', $params);
            return;
        }

        if ($storeId && !$postscategoryId && !$parentId) {
            $store = Mage::app()->getStore($storeId);
            $_prevPostscategoryId = (int)Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId();
            $this->getRequest()->setParam('id', $_prevPostscategoryId);
        }

        if (!($postscategory = $this->_initPostscategory())) {
            return;
        }

        $this->_title($postscategoryId ? $postscategory->getName() : $this->__('New Post Category'));

        $data = Mage::getSingleton('adminhtml/session')->getPostscategoryData(true);
        if (isset($data['postscategory'])) {
            $postscategory->addData($data['postscategory']);
        }

        /**
         * Build response for ajax request
         */
        if ($this->getRequest()->getQuery('isAjax')) {
            $breadcrumbsPath = $postscategory->getPath();
            if (empty($breadcrumbsPath)) {
                $breadcrumbsPath = Mage::getSingleton('admin/session')->getPostscategoryDeletedPath(true);
                if (!empty($breadcrumbsPath)) {
                    $breadcrumbsPath = explode('/', $breadcrumbsPath);
                    if (count($breadcrumbsPath) <= 1) {
                        $breadcrumbsPath = '';
                    } else {
                        array_pop($breadcrumbsPath);
                        $breadcrumbsPath = implode('/', $breadcrumbsPath);
                    }
                }
            }

            Mage::getSingleton('admin/session')
                ->setPostscategoryLastViewedStore($this->getRequest()->getParam('store'));
            Mage::getSingleton('admin/session')
                ->setLastEditedPostscategory($postscategory->getId());
            $this->loadLayout();

            $eventResponse = new Varien_Object(
                array(
                    'content' => $this->getLayout()->getBlock('postscategory.edit')->getFormHtml()
                        . $this->getLayout()->getBlock('postscategory.tree')
                        ->getBreadcrumbsJavascript($breadcrumbsPath, 'editingPostscategoryBreadcrumbs'),
                    'messages' => $this->getLayout()->getMessagesBlock()->getGroupedHtml(),
                )
            );

            Mage::dispatchEvent(
                'postscategory_prepare_ajax_response',
                array(
                    'response' => $eventResponse,
                    'controller' => $this
                )
            );

            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode($eventResponse->getData())
            );

            return;
        }

        $this->loadLayout();
        $this->_setActiveMenu('brander_shop/brander_unitopblog/postscategory');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
            ->setContainerCssClass('postscategories');

        $this->_addBreadcrumb(
            Mage::helper('brander_unitopblog')->__('Manage Post Categories'),
            Mage::helper('catalog')->__('Manage Post Categories')
        );

        $block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($storeId);
        }

        $this->renderLayout();
    }

    /**
     * WYSIWYG editor action for ajax request
     *
     * @access public

     */
    public function wysiwygAction()
    {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock(
            'adminhtml/catalog_helper_form_wysiwyg_content',
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
     * Get tree node (Ajax version)
     *
     * @access public

     */
    public function postscategoriesJsonAction()
    {
        if ($this->getRequest()->getParam('expand_all')) {
            Mage::getSingleton('admin/session')->setPostscategoryIsTreeWasExpanded(true);
        } else {
            Mage::getSingleton('admin/session')->setPostscategoryIsTreeWasExpanded(false);
        }
        if ($postscategoryId = (int) $this->getRequest()->getPost('id')) {
            $this->getRequest()->setParam('id', $postscategoryId);

            if (!$postscategory = $this->_initPostscategory()) {
                return;
            }
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('brander_unitopblog/adminhtml_postscategory_tree')
                    ->getTreeJson($postscategory)
            );
        }
    }

    /**
     * Post Category save
     *
     * @access public

     */
    public function saveAction()
    {
        if (!$postscategory = $this->_initPostscategory()) {
            return;
        }

        $storeId = $this->getRequest()->getParam('store');
        $refreshTree = 'false';
        if ($data = $this->getRequest()->getPost()) {
            $postscategory->addData($data['postscategory']);
            if (!$postscategory->getId()) {
                $parentId = $this->getRequest()->getParam('parent');
                if (!$parentId) {
                    $parentId = Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId();
                }
                $parentPostscategory = Mage::getModel('brander_unitopblog/postscategory')->load($parentId);
                $postscategory->setPath($parentPostscategory->getPath());
            }

            /**
             * Process "Use Config Settings" checkboxes
             */
            if ($useConfig = $this->getRequest()->getPost('use_config')) {
                foreach ($useConfig as $attributeCode) {
                    $postscategory->setData($attributeCode, null);
                }
            }

            $postscategory->setAttributeSetId($postscategory->getDefaultAttributeSetId());

            Mage::dispatchEvent(
                'brander_unitopblog_postscategory_prepare_save',
                array(
                    'postscategory' => $postscategory,
                    'request' => $this->getRequest()
                )
            );

            $postscategory->setData("use_post_data_config", $this->getRequest()->getPost('use_config'));

            try {
                /**
                 * Check "Use Default Value" checkboxes values
                 */
                if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                    foreach ($useDefaults as $attributeCode) {
                        $postscategory->setData($attributeCode, false);
                    }
                }

                /**
                 * Unset $_POST['use_config'] before save
                 */
                $postscategory->unsetData('use_post_data_config');

                $postscategory->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('brander_unitopblog')->__('The post category has been saved.')
                );
                $refreshTree = 'true';
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setPostscategoryData($data);
                $refreshTree = 'false';
            }
        }
        $url = $this->getUrl('*/*/edit', array('_current' => true, 'id' => $postscategory->getId()));
        $this->getResponse()->setBody(
            '<script type="text/javascript">parent.updateContent("' . $url . '", {}, '.$refreshTree.');</script>'
        );
    }

    /**
     * Move post category action
     *
     * @access public

     */
    public function moveAction()
    {
        $postscategory = $this->_initPostscategory();
        if (!$postscategory) {
            $this->getResponse()->setBody(
                Mage::helper('brander_unitopblog')->__('Post Category move error')
            );
            return;
        }
        $parentNodeId   = $this->getRequest()->getPost('pid', false);
        $prevNodeId     = $this->getRequest()->getPost('aid', false);

        try {
            $postscategory->move($parentNodeId, $prevNodeId);
            $this->getResponse()->setBody("SUCCESS");
        } catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        } catch (Exception $e) {
            $this->getResponse()->setBody(
                Mage::helper('brander_unitopblog')->__('Post Category move error')
            );
            Mage::logException($e);
        }

    }

    /**
     * Delete post category action
     *
     * @access public

     */
    public function deleteAction()
    {
        if ($id = (int) $this->getRequest()->getParam('id')) {
            try {
                $postscategory = Mage::getModel('brander_unitopblog/postscategory')->load($id);
                Mage::dispatchEvent(
                    'brander_unitopblog_controller_postscategory_delete',
                    array('postscategory' => $postscategory)
                );

                Mage::getSingleton('admin/session')->setPostscategoryDeletedPath($postscategory->getPath());

                $postscategory->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('brander_unitopblog')->__('The post category has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('brander_unitopblog')->__('An error occurred while trying to delete the post category.')
                );
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('_current'=>true, 'id'=>null)));
    }

    /**
     * Tree Action
     * Retrieve post category tree
     *
     * @access public

     */
    public function treeAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        $postscategoryId = (int) $this->getRequest()->getParam('id');

        if ($storeId) {
            if (!$postscategoryId) {
                $store = Mage::app()->getStore($storeId);
                $rootId = Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId();
                $this->getRequest()->setParam('id', $rootId);
            }
        }

        $postscategory = $this->_initPostscategory();

        $block = $this->getLayout()->createBlock('brander_unitopblog/adminhtml_postscategory_tree');
        $root  = $block->getRoot();
        $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode(
                array(
                    'data' => $block->getTree(),
                    'parameters' => array(
                        'text'         => $block->buildNodeName($root),
                        'draggable'    => false,
                        'allowDrop'    => ($root->getIsVisible()) ? true : false,
                        'id'           => (int) $root->getId(),
                        'expanded'     => (int) $block->getIsWasExpanded(),
                        'store_id'     => (int) $block->getStore()->getId(),
                        'postscategory_id' => (int) $postscategory->getId(),
                        'root_visible' => (int) $root->getIsVisible()
                    )
                )
            )
        );
    }

   /**
    * Build response for refresh input element 'path' in form
    *
    * @access public

    */
    public function refreshPathAction()
    {
        if ($id = (int) $this->getRequest()->getParam('id')) {
            $postscategory = Mage::getModel('brander_unitopblog/postscategory')->load($id);
            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(
                    array(
                       'id' => $id,
                       'path' => $postscategory->getPath(),
                    )
                )
            );
        }
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean

     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('brander_shop/brander_unitopblog/postscategory');
    }
}
