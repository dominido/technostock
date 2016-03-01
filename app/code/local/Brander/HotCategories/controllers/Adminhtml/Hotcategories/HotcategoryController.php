<?php
/**
 * Brander HotCategories extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        HotCategories
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_HotCategories_Adminhtml_Hotcategories_HotcategoryController extends Mage_Adminhtml_Controller_Action
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
        $this->setUsedModuleName('Brander_HotCategories');
    }

    /**
     * init the hot category
     *
     * @access protected 
     * @return Brander_HotCategories_Model_Hotcategory
     */
    protected function _initHotcategory()
    {
        $this->_title($this->__('HotCategories'))
             ->_title($this->__('Manage Hot Categories'));

        $hotcategoryId  = (int) $this->getRequest()->getParam('id');
        $hotcategory    = Mage::getModel('brander_hotcategories/hotcategory')
            ->setStoreId($this->getRequest()->getParam('store', 0));

        if ($hotcategoryId) {
            $hotcategory->load($hotcategoryId);
        }
        Mage::register('current_hotcategory', $hotcategory);
        return $hotcategory;
    }

    /**
     * default action for hotcategory controller
     *
     * @access public
     * @return void
     */
    public function indexAction()
    {
        $this->_title($this->__('HotCategories'))
             ->_title($this->__('Manage Hot Categories'));
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * new hotcategory action
     *
     * @access public
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * edit hotcategory action
     *
     * @access public
     * @return void
     */
    public function editAction()
    {
        $hotcategoryId  = (int) $this->getRequest()->getParam('id');
        $hotcategory    = $this->_initHotcategory();
        if ($hotcategoryId && !$hotcategory->getId()) {
            $this->_getSession()->addError(
                Mage::helper('brander_hotcategories')->__('This hot category no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        if ($data = Mage::getSingleton('adminhtml/session')->getHotcategoryData(true)) {
            $hotcategory->setData($data);
        }
        $this->_title($hotcategory->getTitle());
        Mage::dispatchEvent(
            'brander_hotcategories_hotcategory_edit_action',
            array('hotcategory' => $hotcategory)
        );
        $this->loadLayout();
        if ($hotcategory->getId()) {
            if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
                $switchBlock->setDefaultStoreName(Mage::helper('brander_hotcategories')->__('Default Values'))
                    ->setWebsiteIds($hotcategory->getWebsiteIds())
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
     * save hot category action
     *
     * @access public
     * @return void
     */
    public function saveAction()
    {
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $hotcategoryId   = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);
        $data = $this->getRequest()->getPost();
        if ($data) {
            $hotcategory     = $this->_initHotcategory();
            $hotcategoryData = $this->getRequest()->getPost('hotcategory', array());
            $hotcategory->addData($hotcategoryData);
            $hotcategory->setAttributeSetId($hotcategory->getDefaultAttributeSetId());
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $attributeCode) {
                    $hotcategory->setData($attributeCode, false);
                }
            }
            try {
                $hotcategory->save();
                $hotcategoryId = $hotcategory->getId();
                $this->_getSession()->addSuccess(
                    Mage::helper('brander_hotcategories')->__('Hot Category was saved')
                );
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage())
                    ->setHotcategoryData($hotcategoryData);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError(
                    Mage::helper('brander_hotcategories')->__('Error saving hot category')
                )
                ->setHotcategoryData($hotcategoryData);
                $redirectBack = true;
            }
        }
        if ($redirectBack) {
            $this->_redirect(
                '*/*/edit',
                array(
                    'id'    => $hotcategoryId,
                    '_current'=>true
                )
            );
        } else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }

    /**
     * delete hot category
     *
     * @access public
     * @return void
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $hotcategory = Mage::getModel('brander_hotcategories/hotcategory')->load($id);
            try {
                $hotcategory->delete();
                $this->_getSession()->addSuccess(
                    Mage::helper('brander_hotcategories')->__('The hot categories has been deleted.')
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
     * mass delete hot categories
     *
     * @access public
     * @return void
     */
    public function massDeleteAction()
    {
        $hotcategoryIds = $this->getRequest()->getParam('hotcategory');
        if (!is_array($hotcategoryIds)) {
            $this->_getSession()->addError($this->__('Please select hot categories.'));
        } else {
            try {
                foreach ($hotcategoryIds as $hotcategoryId) {
                    $hotcategory = Mage::getSingleton('brander_hotcategories/hotcategory')->load($hotcategoryId);
                    Mage::dispatchEvent(
                        'brander_hotcategories_controller_hotcategory_delete',
                        array('hotcategory' => $hotcategory)
                    );
                    $hotcategory->delete();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('brander_hotcategories')->__('Total of %d record(s) have been deleted.', count($hotcategoryIds))
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
        $hotcategoryIds = $this->getRequest()->getParam('hotcategory');
        if (!is_array($hotcategoryIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('brander_hotcategories')->__('Please select hot categories.')
            );
        } else {
            try {
                foreach ($hotcategoryIds as $hotcategoryId) {
                $hotcategory = Mage::getSingleton('brander_hotcategories/hotcategory')->load($hotcategoryId)
                    ->setStatus($this->getRequest()->getParam('status'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d hot categories were successfully updated.', count($hotcategoryIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('brander_hotcategories')->__('There was an error updating hot categories.')
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
        return Mage::getSingleton('admin/session')->isAllowed('brander_shop/shop_content/hotcategory');
    }

    /**
     * Export hotcategories in CSV format
     *
     * @access public
     * @return void
     */
    public function exportCsvAction()
    {
        $fileName   = 'hotcategories.csv';
        $content    = $this->getLayout()->createBlock('brander_hotcategories/adminhtml_hotcategory_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export hot categories in Excel format
     *
     * @access public
     * @return void
     */
    public function exportExcelAction()
    {
        $fileName   = 'hotcategory.xls';
        $content    = $this->getLayout()->createBlock('brander_hotcategories/adminhtml_hotcategory_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export hot categories in XML format
     *
     * @access public
     * @return void
     */
    public function exportXmlAction()
    {
        $fileName   = 'hotcategory.xml';
        $content    = $this->getLayout()->createBlock('brander_hotcategories/adminhtml_hotcategory_grid')
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
            'brander_hotcategories/adminhtml_hotcategories_helper_form_wysiwyg_content',
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
     * mass Category Mode change
     *
     * @access public
     * @return void
     */
    public function massModeAction()
    {
        $hotcategoryIds = (array)$this->getRequest()->getParam('hotcategory');
        $storeId       = (int)$this->getRequest()->getParam('store', 0);
        $flag          = (int)$this->getRequest()->getParam('flag_mode');
        if ($flag == 2) {
            $flag = 0;
        }
        try {
            foreach ($hotcategoryIds as $hotcategoryId) {
                $hotcategory = Mage::getSingleton('brander_hotcategories/hotcategory')
                    ->setStoreId($storeId)
                    ->load($hotcategoryId);
                $hotcategory->setMode($flag)->save();
            }
            $this->_getSession()->addSuccess(
                Mage::helper('brander_hotcategories')->__('Total of %d record(s) have been updated.', count($hotcategoryIds))
            );
        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('brander_hotcategories')->__('An error occurred while updating the hot categories.')
            );
        }
        $this->_redirect('*/*/', array('store'=> $storeId));
    }
}
