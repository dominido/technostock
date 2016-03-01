<?php
/**
 * Brander Benefits extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        Benefits
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_Benefits_Adminhtml_Benefits_BenefitController extends Mage_Adminhtml_Controller_Action
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
        $this->setUsedModuleName('Brander_Benefits');
    }

    /**
     * init the benefit
     *
     * @access protected 
     * @return Brander_Benefits_Model_Benefit
     */
    protected function _initBenefit()
    {
        $this->_title($this->__('Benefits'))
             ->_title($this->__('Manage Benefits'));

        $benefitId  = (int) $this->getRequest()->getParam('id');
        $benefit    = Mage::getModel('brander_benefits/benefit')
            ->setStoreId($this->getRequest()->getParam('store', 0));

        if ($benefitId) {
            $benefit->load($benefitId);
        }
        Mage::register('current_benefit', $benefit);
        return $benefit;
    }

    /**
     * default action for benefit controller
     *
     * @access public
     * @return void
     */
    public function indexAction()
    {
        $this->_title($this->__('Benefits'))
             ->_title($this->__('Manage Benefits'));
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * new benefit action
     *
     * @access public
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * edit benefit action
     *
     * @access public
     * @return void
     */
    public function editAction()
    {
        $benefitId  = (int) $this->getRequest()->getParam('id');
        $benefit    = $this->_initBenefit();
        if ($benefitId && !$benefit->getId()) {
            $this->_getSession()->addError(
                Mage::helper('brander_benefits')->__('This benefit no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        if ($data = Mage::getSingleton('adminhtml/session')->getBenefitData(true)) {
            $benefit->setData($data);
        }
        $this->_title($benefit->getTitle());
        Mage::dispatchEvent(
            'brander_benefits_benefit_edit_action',
            array('benefit' => $benefit)
        );
        $this->loadLayout();
        if ($benefit->getId()) {
            if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
                $switchBlock->setDefaultStoreName(Mage::helper('brander_benefits')->__('Default Values'))
                    ->setWebsiteIds($benefit->getWebsiteIds())
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
     * save benefit action
     *
     * @access public
     * @return void
     */
    public function saveAction()
    {
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $benefitId   = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);
        $data = $this->getRequest()->getPost();
        if ($data) {
            $benefit     = $this->_initBenefit();
            $benefitData = $this->getRequest()->getPost('benefit', array());
            $benefit->addData($benefitData);
            $benefit->setAttributeSetId($benefit->getDefaultAttributeSetId());
                $products = $this->getRequest()->getPost('products', -1);
                if ($products != -1) {
                    $benefit->setProductsData(
                        Mage::helper('adminhtml/js')->decodeGridSerializedInput($products)
                    );
                }
                $categories = $this->getRequest()->getPost('category_ids', -1);
                if ($categories != -1) {
                    $categories = explode(',', $categories);
                    $categories = array_unique($categories);
                    $benefit->setCategoriesData($categories);
                }
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $attributeCode) {
                    $benefit->setData($attributeCode, false);
                }
            }
            try {
                $benefit->save();
                $benefitId = $benefit->getId();
                $this->_getSession()->addSuccess(
                    Mage::helper('brander_benefits')->__('Benefit was saved')
                );
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage())
                    ->setBenefitData($benefitData);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError(
                    Mage::helper('brander_benefits')->__('Error saving benefit')
                )
                ->setBenefitData($benefitData);
                $redirectBack = true;
            }
        }
        if ($redirectBack) {
            $this->_redirect(
                '*/*/edit',
                array(
                    'id'    => $benefitId,
                    '_current'=>true
                )
            );
        } else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }

    /**
     * delete benefit
     *
     * @access public
     * @return void
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $benefit = Mage::getModel('brander_benefits/benefit')->load($id);
            try {
                $benefit->delete();
                $this->_getSession()->addSuccess(
                    Mage::helper('brander_benefits')->__('The benefits has been deleted.')
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
     * mass delete benefits
     *
     * @access public
     * @return void
     */
    public function massDeleteAction()
    {
        $benefitIds = $this->getRequest()->getParam('benefit');
        if (!is_array($benefitIds)) {
            $this->_getSession()->addError($this->__('Please select benefits.'));
        } else {
            try {
                foreach ($benefitIds as $benefitId) {
                    $benefit = Mage::getSingleton('brander_benefits/benefit')->load($benefitId);
                    Mage::dispatchEvent(
                        'brander_benefits_controller_benefit_delete',
                        array('benefit' => $benefit)
                    );
                    $benefit->delete();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('brander_benefits')->__('Total of %d record(s) have been deleted.', count($benefitIds))
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
        $benefitIds = $this->getRequest()->getParam('benefit');
        if (!is_array($benefitIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('brander_benefits')->__('Please select benefits.')
            );
        } else {
            try {
                foreach ($benefitIds as $benefitId) {
                $benefit = Mage::getSingleton('brander_benefits/benefit')->load($benefitId)
                    ->setStatus($this->getRequest()->getParam('status'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d benefits were successfully updated.', count($benefitIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('brander_benefits')->__('There was an error updating benefits.')
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
        return Mage::getSingleton('admin/session')->isAllowed('brander_shop/benefit');
    }

    /**
     * Export benefits in CSV format
     *
     * @access public
     * @return void
     */
    public function exportCsvAction()
    {
        $fileName   = 'benefits.csv';
        $content    = $this->getLayout()->createBlock('brander_benefits/adminhtml_benefit_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export benefits in Excel format
     *
     * @access public
     * @return void
     */
    public function exportExcelAction()
    {
        $fileName   = 'benefit.xls';
        $content    = $this->getLayout()->createBlock('brander_benefits/adminhtml_benefit_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export benefits in XML format
     *
     * @access public
     * @return void
     */
    public function exportXmlAction()
    {
        $fileName   = 'benefit.xml';
        $content    = $this->getLayout()->createBlock('brander_benefits/adminhtml_benefit_grid')
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
            'brander_benefits/adminhtml_benefits_helper_form_wysiwyg_content',
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
     * mass Show Title on Front change
     *
     * @access public
     * @return void
     */
    public function massTitleOnFrontAction()
    {
        $benefitIds = (array)$this->getRequest()->getParam('benefit');
        $storeId       = (int)$this->getRequest()->getParam('store', 0);
        $flag          = (int)$this->getRequest()->getParam('flag_title_on_front');
        if ($flag == 2) {
            $flag = 0;
        }
        try {
            foreach ($benefitIds as $benefitId) {
                $benefit = Mage::getSingleton('brander_benefits/benefit')
                    ->setStoreId($storeId)
                    ->load($benefitId);
                $benefit->setTitleOnFront($flag)->save();
            }
            $this->_getSession()->addSuccess(
                Mage::helper('brander_benefits')->__('Total of %d record(s) have been updated.', count($benefitIds))
            );
        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('brander_benefits')->__('An error occurred while updating the benefits.')
            );
        }
        $this->_redirect('*/*/', array('store'=> $storeId));
    }

    /**
     * mass Show on homepage change
     *
     * @access public
     * @return void
     */
    public function massShowOnHomepageAction()
    {
        $benefitIds = (array)$this->getRequest()->getParam('benefit');
        $storeId       = (int)$this->getRequest()->getParam('store', 0);
        $flag          = (int)$this->getRequest()->getParam('flag_show_on_homepage');
        if ($flag == 2) {
            $flag = 0;
        }
        try {
            foreach ($benefitIds as $benefitId) {
                $benefit = Mage::getSingleton('brander_benefits/benefit')
                    ->setStoreId($storeId)
                    ->load($benefitId);
                $benefit->setShowOnHomepage($flag)->save();
            }
            $this->_getSession()->addSuccess(
                Mage::helper('brander_benefits')->__('Total of %d record(s) have been updated.', count($benefitIds))
            );
        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('brander_benefits')->__('An error occurred while updating the benefits.')
            );
        }
        $this->_redirect('*/*/', array('store'=> $storeId));
    }

    /**
     * get grid of products action
     *
     * @access public
     * @return void
     */
    public function productsAction()
    {
        $this->_initBenefit();
        $this->loadLayout();
        $this->getLayout()->getBlock('benefit.edit.tab.product')
            ->setBenefitProducts($this->getRequest()->getPost('benefit_products', null));
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
        $this->_initBenefit();
        $this->loadLayout();
        $this->getLayout()->getBlock('benefit.edit.tab.product')
            ->setBenefitProducts($this->getRequest()->getPost('benefit_products', null));
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
        $this->_initBenefit();
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
        $this->_initBenefit();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('brander_benefits/adminhtml_benefit_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
}
