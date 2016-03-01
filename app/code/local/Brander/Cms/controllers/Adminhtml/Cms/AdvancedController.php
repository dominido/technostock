<?php
class Brander_Cms_Adminhtml_Cms_AdvancedController extends Mage_Adminhtml_Controller_Action
{
    protected function _initPage($getRootInstead = false)
    {
        $this->_title($this->__('Advanced CMS'))
             ->_title($this->__('Pages'))
             ->_title($this->__('Manage Pages'));

        $pageId = (int) $this->getRequest()->getParam('id',false);
        $storeId    = (int) $this->getRequest()->getParam('store');
        $page = Mage::getModel('cmsadvanced/page');
        $page->setStoreId($storeId);
   
        if ($pageId) {
            $page->load($pageId);
            if ($storeId) {
                $rootId = 1;
            }
        }

        if ($activeTabId = (string) $this->getRequest()->getParam('active_tab_id')) {
            Mage::getSingleton('admin/session')->setActiveTabId($activeTabId);
        }

        Mage::register('page', $page);
        Mage::register('current_page', $page);
    
        return $page;
    }
    
    public function indexAction()
    {
        $this->_forward('edit');
    }

    public function pagesJsonAction()
    {
        if ($this->getRequest()->getParam('expand_all')) {
            Mage::getSingleton('admin/session')->setIsTreeWasExpanded(true);
        } else {
            Mage::getSingleton('admin/session')->setIsTreeWasExpanded(false);
        }
        if ($pageId = (int) $this->getRequest()->getPost('id')) {
            $this->getRequest()->setParam('id', $pageId);

            if (!$page = $this->_initPage()) {
                return;
            }
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('cmsadvanced/adminhtml_page_tree')
                    ->getTreeJson($page)
            );
        }
    }
    
    /**
     * Edit page page
     */
    public function editAction()
    {
        $params['_current'] = true;
        $redirect = false;

        $storeId = (int) $this->getRequest()->getParam('store');
        $parentId = (int) $this->getRequest()->getParam('parent');
        $_prevStoreId = Mage::getSingleton('admin/session')
            ->getLastViewedStore(true);

        if ($_prevStoreId != null && !$this->getRequest()->getQuery('isAjax')) {
            $params['store'] = $_prevStoreId;
            $redirect = true;
        }

        $pageId = (int) $this->getRequest()->getParam('id');
        $_prevPageId = Mage::getSingleton('admin/session')
            ->getLastEditedPage(true);

        if ($_prevPageId
            && !$this->getRequest()->getQuery('isAjax')
            && !$this->getRequest()->getParam('clear')) {
             $this->getRequest()->setParam('id',$_prevPageId);
        }

         if ($redirect) {
            $this->_redirect('*/*/edit', $params);
            return;
        }

        if ($storeId && !$pageId && !$parentId) {
            $store = Mage::app()->getStore($storeId);
            $_prevPageId = 1;
            $this->getRequest()->setParam('id', $_prevPageId);
        }

        if (!($page = $this->_initPage(true))) {
            return;
        }

        $this->_title($pageId ? $page->getName() : $this->__('New Page'));

        /**
         * Check if we have data in session (if duering page save was exceprion)
         */
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (isset($data['general'])) {
            $page->addData($data['general']);
        }
        
        /**
         * Build response for ajax request
         */
        if ($this->getRequest()->getQuery('isAjax')) {
            // prepare breadcrumbs of selected page, if any
            $breadcrumbsPath = $page->getPath();
            if (empty($breadcrumbsPath)) {
                // but if no page, and it is deleted - prepare breadcrumbs from path, saved in session
                $breadcrumbsPath = Mage::getSingleton('admin/session')->getDeletedPath(true);
                if (!empty($breadcrumbsPath)) {
                    $breadcrumbsPath = explode('/', $breadcrumbsPath);
                    // no need to get parent breadcrumbs if deleting page level 1
                    if (count($breadcrumbsPath) <= 1) {
                        $breadcrumbsPath = '';
                    }
                    else {
                        array_pop($breadcrumbsPath);
                        $breadcrumbsPath = implode('/', $breadcrumbsPath);
                    }
                }
            }

            Mage::getSingleton('admin/session')->setLastViewedStore($this->getRequest()->getParam('store'));
            Mage::getSingleton('admin/session')->setLastEditedPage($page->getId());

            $this->loadLayout();
    
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
                'messages' => $this->getLayout()->getMessagesBlock()->getGroupedHtml(),
                'content' =>
                    $this->getLayout()->getBlock('cms.advanced.edit')->getFormHtml()
                    . $this->getLayout()->getBlock('cms.advanced.tree')
                        ->getBreadcrumbsJavascript($breadcrumbsPath, 'editingPageBreadcrumbs')
            )));
            return;
        }
        
        $this->loadLayout();
        $this->_setActiveMenu('cms_advanced');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)->setCanLoadTinyMce(true)
            ->setContainerCssClass('catalog-categories');

        if ($js = $this->getLayout()->getBlock('js')) {
            $js->append($this->getLayout()->createBlock('core/template', '',array('template'=>'catalog/wysiwyg/js.phtml')));
        }

        $this->_addBreadcrumb(Mage::helper('catalog')->__('Manage Catalog Categories'),
             Mage::helper('catalog')->__('Manage Categories')
        );

        $this->renderLayout();
    }

    /**
     * Page save
     */
    public function saveAction()
    {
        if (!$page = $this->_initPage()) {
            return;
        }

        $storeId = $this->getRequest()->getParam('store');
        $refreshTree = 'false';
        if ($data = $this->getRequest()->getPost()) {
            $page->addData($data['general']);
            if (!$page->getId()) {
                $parentId = $this->getRequest()->getParam('parent');
                if (!$parentId) {
                    if ($storeId) {
                        $parentId = 1;
                    }
                    else {
                        $parentId = 1;
                    }
                }
                $parentPage = Mage::getModel('cmsadvanced/page')->load($parentId);
                $page->setPath($parentPage->getPath());
            }

            $page->setStoreId($storeId);
            /**
             * Process "Use Config Settings" checkboxes
             */
            if ($useConfig = $this->getRequest()->getPost('use_config')) {
                foreach ($useConfig as $attributeCode) {
                    $page->setData($attributeCode, null);
                }
            }

            $page->setAttributeSetId($page->getDefaultAttributeSetId());

            /**
             * Proceed with $_POST['use_config']
             * set into page model for proccessing through validation
             */
            $page->setData("use_post_data_config", $this->getRequest()->getPost('use_config'));

            try {
                /**
                 * Check "Use Default Value" checkboxes values
                 */
                if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                    foreach ($useDefaults as $attributeCode) {
                        $page->setData($attributeCode, false);
                    }
                }

                /**
                 * Unset $_POST['use_config'] before save
                 */
                $page->unsetData('use_post_data_config');
                $page->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('catalog')->__('The page has been saved.'));
                $refreshTree = 'true';
            }
            catch (Exception $e){
                $this->_getSession()->addError($e->getMessage())
                    ->setPageData($data);
                $refreshTree = 'false';
            }
        }
        $url = $this->getUrl('*/*/edit', array('_current' => true, 'id' => $page->getId()));
        $this->getResponse()->setBody(
            '<script type="text/javascript">parent.updateContent("' . $url . '", {}, '.$refreshTree.');</script>'
        );
    }

    /**
     * Add new page form
     */
    public function addAction()
    {
        Mage::getSingleton('admin/session')->unsActiveTabId();
        $this->_forward('edit');
    }

    public function deleteAction()
    {
        if ($id = (int) $this->getRequest()->getParam('id')) {
            try {
                $page = Mage::getModel('cmsadvanced/page')->load($id);

                Mage::getSingleton('admin/session')->setDeletedPath($page->getPath());

                $page->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('catalog')->__('The page has been deleted.'));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
                return;
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('An error occurred while trying to delete the page.'));
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('_current'=>true, 'id'=>null)));
    }

    public function wysiwygAction()
    {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $content = $this->getLayout()->createBlock('adminhtml/catalog_helper_form_wysiwyg_content', '', array(
            'editor_element_id' => $elementId
        ));
        $this->getResponse()->setBody($content->toHtml());
    }

    public function gridAction()
    {
    }

    /**
     * Move page action
     */
    public function moveAction()
    {
        $page = $this->_initPage();
        if (!$page) {
            $this->getResponse()->setBody(Mage::helper('catalog')->__('Page move error'));
            return;
        }
        /**
         * New parent page identifier
         */
        $parentNodeId   = $this->getRequest()->getPost('pid', false);
        /**
         * page id after which we have put our page
         */
        $prevNodeId     = $this->getRequest()->getPost('aid', false);

        try {
            $page->move($parentNodeId, $prevNodeId);
            $this->getResponse()->setBody("SUCCESS");
        }
        catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        }
        catch (Exception $e){
            $this->getResponse()->setBody(Mage::helper('catalog')->__('Page move error'.$e));
            Mage::logException($e);
        }

    }

    public function treeAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        $pageId = (int) $this->getRequest()->getParam('id');

        if ($storeId) {
            if (!$pageId) {
                $store = Mage::app()->getStore($storeId);
                $rootId = 1;
                $this->getRequest()->setParam('id', $rootId);
            }
        }

        $page = $this->_initPage(true);

        $block = $this->getLayout()->createBlock('cmsadvanced/adminhtml_page_tree');
        $root  = $block->getRoot();

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'data' => $block->getTree(),
            'parameters' => array(
                'text'        => $block->buildNodeName($root),
                'draggable'   => false,
                'allowDrop'   => ($root->getIsVisible()) ? true : false,
                'id'          => (int) $root->getId(),
                'expanded'    => (int) $block->getIsWasExpanded(),
                'store_id'    => (int) $block->getStore()->getId(),
                'category_id' => (int) $page->getId(),
                'root_visible'=> (int) $root->getIsVisible()
        ))));
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
        return Mage::getSingleton('admin/session')->isAllowed('brander_shop/cms_advanced');
    }
}
