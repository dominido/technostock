<?php

class Brander_LayeredNavigation_Adminhtml_FilterController extends Mage_Adminhtml_Controller_Action
{
    // show grid
    public function indexAction()
    {
        $this->_checkRootCategories();
        $this->_checkOldTemplates();
        $this->_checkConflicts();

        $this->loadLayout();
        $this->_setActiveMenu('catalog/layerednavigation');
        $this->_addBreadcrumb($this->__('Filters'), $this->__('Filters'));
        $this->_addContent($this->getLayout()->createBlock('brander_layerednavigation/adminhtml_filter'));
        $this->_title($this->__('Layered Navigation Filters'));
        $this->renderLayout();
    }

    protected function _checkRootCategories()
    {
        foreach (Mage::app()->getStores() as $store){
            $category = Mage::getModel('catalog/category')
                ->setStoreId($store->getId())
                ->load($store->getRootCategoryId());

            if (!$category->getIsAnchor()){
                $msg = $this->__('Please open Catalog > Manage Categories and set property "Is Anchor" to "Yes" for the store root category.');
                $this->_getSession()->addNotice($msg);
                break;
            }
        }
    }

    protected function _checkOldTemplates()
    {
        $frontendPath = rtrim(Mage::getBaseDir('design') . '/frontend', ' /');

        foreach (Mage::app()->getStores() as $store){
            $package = Mage::getStoreConfig('design/package/name', $store);
            if (!$package)
                $package = 'default';

            $theme = Mage::getStoreConfig('design/theme/skin', $store);
            if (!$theme)
                $theme = 'default';

            $themePath = $frontendPath . '/' . trim($package, ' /') . '/' . trim($theme, ' /');
            $excessPath = $themePath . '/template/brander/layerednavigation';

            if (is_dir($excessPath)){
                $msg = $this->__('In case you need to modify the module templates please copy files from app/design/frontend/base/default/template/brander/layerednavigation/  to your custom theme  app/design/frontend/PACKAGE/THEME/template/brander/layerednavigation/');
                $this->_getSession()->addNotice($msg);
                break;
            }
        }
    }

    protected function _checkConflicts()
    {
        $classes = array(
            'model' => array(
                'catalog/layer_filter_price',
                'catalog/layer_filter_decimal',
                'catalog/layer_filter_attribute',
                'catalog/layer_filter_category',
                'catalog/layer_filter_item',
                'catalogsearch/layer_filter_attribute',
            ),
            'block' => array(
                'catalog/layer_filter_attribute',
                'catalog/product_list_toolbar',
                'catalogsearch/layer_filter_attribute',
            ),
        );

        // TODO:: del it
/*        foreach ($classes as $type => $names){
            foreach ($names as $name){
                $name = Mage::getConfig()->getGroupedClassName($type, $name);
                if (substr($name, 0, 6) != 'Amasty'){
                    $msg = $this->__('There is a conflict(s) with some other extension: class %s. If the module works incorrect, consider our <a href="http://amasty.com/installation-service.html">Installation Service</a>.', $name);
                    $this->_getSession()->addNotice($msg);
                    break(2);
                }
            }
        }*/
    }

    // load filters and their options
    public function newAction()
    {
        Mage::getResourceModel('brander_layerednavigation/filter')->createFilters();
        $this->invalidateCache();
        $this->_redirect('*/*/');
    }

    // edit filters (uses tabs)
    public function editAction()
    {
        $id     = (int) $this->getRequest()->getParam('id');
        $model  = Mage::getModel('brander_layerednavigation/filter')->load($id);

        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brander_layerednavigation')->__('Filter does not exist'));
            $this->_redirect('*/*/');
            return;
        }

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('brander_layerednavigation_filter', $model);

        $this->loadLayout();

        $this->_setActiveMenu('catalog/layerednavigation');
        $this->_addContent($this->getLayout()->createBlock('brander_layerednavigation/adminhtml_filter_edit'))
             ->_addLeft($this->getLayout()->createBlock('brander_layerednavigation/adminhtml_filter_edit_tabs'));

        $this->_title($this->__('Edit Filter'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('brander_layerednavigation/filter');
        $data = $this->getRequest()->getPost();
        if ($data) {
            $model->setData($data);
            $model->setId($id);

            if ($model->getData('display_type') == Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Price::DT_FROMTO) {
                $model->setData('from_to_widget', true);
            }

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                $msg = Mage::helper('brander_layerednavigation')->__('Filter properties have been successfully saved');
                Mage::getSingleton('adminhtml/session')->addSuccess($msg);
                if ($this->getRequest()->getParam('continue')){
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                }
                else {
                    $this->_redirect('*/*');
                }

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $id));
            }

            $this->invalidateCache();
            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brander_layerednavigation')->__('Unable to find a filter to save'));
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('filter_id');
        if(!is_array($ids)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brander_layerednavigation')->__('Please select filter(s)'));
        }
        else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('brander_layerednavigation/filter')->load($id);
                    $model->delete();
                    // todo delete values or add a foreign key
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            $this->invalidateCache();
        }
        $this->_redirect('*/*/');
    }

    protected function invalidateCache()
    {
        /** @var Brander_LayeredNavigation_Helper_Data $helper */
        $helper = Mage::helper('brander_layerednavigation');
        $helper->invalidateCache();
    }

    //for ajax
    public function valuesAction()
    {
        $id = (int) $this->getRequest()->getParam('id');
        $model = Mage::getModel('brander_layerednavigation/filter');

        if ($id) {
            $model->load($id);
        }

        Mage::register('brander_layerednavigation_filter', $model);

        $this->getResponse()->setBody($this->getLayout()
            ->createBlock('brander_layerednavigation/adminhtml_filter_edit_tab_values')->toHtml());
    }
}
