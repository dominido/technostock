<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Category Image admin controller
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
class Brander_CategoryBanner_Adminhtml_Categorybanner_CategorybannerController extends Brander_CategoryBanner_Controller_Adminhtml_CategoryBanner
{
    /**
     * init the category image
     *
     * @access protected
     * @return Brander_CategoryBanner_Model_Categorybanner
     */
    protected function _initCategorybanner()
    {
        $categorybannerId  = (int) $this->getRequest()->getParam('id');
        $categorybanner    = Mage::getModel('brander_categorybanner/categorybanner');
        if ($categorybannerId) {
            $categorybanner->load($categorybannerId);
        }
        Mage::register('current_categorybanner', $categorybanner);
        return $categorybanner;
    }

    /**
     * default action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('brander_categorybanner')->__('Category Banner'))
             ->_title(Mage::helper('brander_categorybanner')->__('Category Image'));
        $this->renderLayout();
    }

    /**
     * grid action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * edit category image - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $categorybannerId    = $this->getRequest()->getParam('id');
        $categorybanner      = $this->_initCategorybanner();
        if ($categorybannerId && !$categorybanner->getId()) {
            $this->_getSession()->addError(
                Mage::helper('brander_categorybanner')->__('This category image no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getCategorybannerData(true);
        if (!empty($data)) {
            $categorybanner->setData($data);
        }
        Mage::register('categorybanner_data', $categorybanner);
        $this->loadLayout();
        $this->_title(Mage::helper('brander_categorybanner')->__('Category Banner'))
             ->_title(Mage::helper('brander_categorybanner')->__('Category Image'));
        if ($categorybanner->getId()) {
            $this->_title($categorybanner->getCategoryImageName());
        } else {
            $this->_title(Mage::helper('brander_categorybanner')->__('Add category image'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new category image action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save category image - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('categorybanner')) {
            try {
                $categorybanner = $this->_initCategorybanner();
                $categorybanner->addData($data);
                $categoryImageName = $this->_uploadAndGetName(
                    'category_image',
                    Mage::helper('brander_categorybanner/categorybanner_image')->getImageBaseDir(),
                    $data
                );
                $categorybanner->setData('category_image', $categoryImageName);
                $categories = $this->getRequest()->getPost('category_ids', -1);
                if ($categories != -1) {
                    $categories = explode(',', $categories);
                    $categories = array_unique($categories);
                    $categorybanner->setCategoriesData($categories);
                }
                $categorybanner->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('brander_categorybanner')->__('Category Image was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $categorybanner->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                if (isset($data['category_image']['value'])) {
                    $data['category_image'] = $data['category_image']['value'];
                }
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCategorybannerData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                if (isset($data['category_image']['value'])) {
                    $data['category_image'] = $data['category_image']['value'];
                }
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('brander_categorybanner')->__('There was a problem saving the category image.')
                );
                Mage::getSingleton('adminhtml/session')->setCategorybannerData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('brander_categorybanner')->__('Unable to find category image to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete category image - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $categorybanner = Mage::getModel('brander_categorybanner/categorybanner');
                $categorybanner->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('brander_categorybanner')->__('Category Image was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('brander_categorybanner')->__('There was an error deleting category image.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('brander_categorybanner')->__('Could not find category image to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete category image - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $categorybannerIds = $this->getRequest()->getParam('categorybanner');
        if (!is_array($categorybannerIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('brander_categorybanner')->__('Please select category image to delete.')
            );
        } else {
            try {
                foreach ($categorybannerIds as $categorybannerId) {
                    $categorybanner = Mage::getModel('brander_categorybanner/categorybanner');
                    $categorybanner->setId($categorybannerId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('brander_categorybanner')->__('Total of %d category image were successfully deleted.', count($categorybannerIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('brander_categorybanner')->__('There was an error deleting category image.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass status change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massStatusAction()
    {
        $categorybannerIds = $this->getRequest()->getParam('categorybanner');
        if (!is_array($categorybannerIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('brander_categorybanner')->__('Please select category image.')
            );
        } else {
            try {
                foreach ($categorybannerIds as $categorybannerId) {
                $categorybanner = Mage::getSingleton('brander_categorybanner/categorybanner')->load($categorybannerId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d category image were successfully updated.', count($categorybannerIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('brander_categorybanner')->__('There was an error updating category image.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * get categories action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function categoriesAction()
    {
        $this->_initCategorybanner();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * get child categories action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function categoriesJsonAction()
    {
        $this->_initCategorybanner();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('brander_categorybanner/adminhtml_categorybanner_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    /**
     * export as csv - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportCsvAction()
    {
        $fileName   = 'categorybanner.csv';
        $content    = $this->getLayout()->createBlock('brander_categorybanner/adminhtml_categorybanner_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportExcelAction()
    {
        $fileName   = 'categorybanner.xls';
        $content    = $this->getLayout()->createBlock('brander_categorybanner/adminhtml_categorybanner_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportXmlAction()
    {
        $fileName   = 'categorybanner.xml';
        $content    = $this->getLayout()->createBlock('brander_categorybanner/adminhtml_categorybanner_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean
     * @author Ultimate Module Creator
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('brander_categorybanner/categorybanner');
    }
}
