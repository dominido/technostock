<?php
/**
 * Brander ProductBanners extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        ProductBanners
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_ProductBanners_Adminhtml_Productbanners_BannerController extends Mage_Adminhtml_Controller_Action
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
        $this->setUsedModuleName('Brander_ProductBanners');
    }

    /**
     * init the banner
     *
     * @access protected 
     * @return Brander_ProductBanners_Model_Banner

     */
    protected function _initBanner()
    {
        $this->_title($this->__('Product Banners'))
             ->_title($this->__('Manage Banners'));

        $bannerId  = (int) $this->getRequest()->getParam('id');
        $banner    = Mage::getModel('brander_productbanners/banner')
            ->setStoreId($this->getRequest()->getParam('store', 0));

        if ($bannerId) {
            $banner->load($bannerId);
        }
        Mage::register('current_banner', $banner);
        return $banner;
    }

    /**
     * default action for banner controller
     *
     * @access public
     * @return void

     */
    public function indexAction()
    {
        $this->_title($this->__('Product Banners'))
             ->_title($this->__('Manage Banners'));
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * new banner action
     *
     * @access public
     * @return void

     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * edit banner action
     *
     * @access public
     * @return void

     */
    public function editAction()
    {
        $bannerId  = (int) $this->getRequest()->getParam('id');
        $banner    = $this->_initBanner();
        if ($bannerId && !$banner->getId()) {
            $this->_getSession()->addError(
                Mage::helper('brander_productbanners')->__('This banner no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        if ($data = Mage::getSingleton('adminhtml/session')->getBannerData(true)) {
            $banner->setData($data);
        }
        $this->_title($banner->getTitle());
        Mage::dispatchEvent(
            'brander_productbanners_banner_edit_action',
            array('banner' => $banner)
        );
        $this->loadLayout();
        if ($banner->getId()) {
            if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
                $switchBlock->setDefaultStoreName(Mage::helper('brander_productbanners')->__('Default Values'))
                    ->setWebsiteIds($banner->getWebsiteIds())
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
     * save banner action
     *
     * @access public
     * @return void

     */
    public function saveAction()
    {
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $bannerId   = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);
        $data = $this->getRequest()->getPost();
        if ($data) {
            $banner     = $this->_initBanner();
            $bannerData = $this->getRequest()->getPost('banner', array());
            $banner->addData($bannerData);
            $banner->setAttributeSetId($banner->getDefaultAttributeSetId());
                $products = $this->getRequest()->getPost('products', -1);
                if ($products != -1) {
                    $banner->setProductsData(
                        Mage::helper('adminhtml/js')->decodeGridSerializedInput($products)
                    );
                }
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $attributeCode) {
                    $banner->setData($attributeCode, false);
                }
            }
            try {
                $banner->save();
                $bannerId = $banner->getId();
                $this->_getSession()->addSuccess(
                    Mage::helper('brander_productbanners')->__('Banner was saved')
                );
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage())
                    ->setBannerData($bannerData);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError(
                    Mage::helper('brander_productbanners')->__('Error saving banner')
                )
                ->setBannerData($bannerData);
                $redirectBack = true;
            }
        }
        if ($redirectBack) {
            $this->_redirect(
                '*/*/edit',
                array(
                    'id'    => $bannerId,
                    '_current'=>true
                )
            );
        } else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }

    /**
     * delete banner
     *
     * @access public
     * @return void

     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $banner = Mage::getModel('brander_productbanners/banner')->load($id);
            try {
                $banner->delete();
                $this->_getSession()->addSuccess(
                    Mage::helper('brander_productbanners')->__('The banners has been deleted.')
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
     * mass delete banners
     *
     * @access public
     * @return void

     */
    public function massDeleteAction()
    {
        $bannerIds = $this->getRequest()->getParam('banner');
        if (!is_array($bannerIds)) {
            $this->_getSession()->addError($this->__('Please select banners.'));
        } else {
            try {
                foreach ($bannerIds as $bannerId) {
                    $banner = Mage::getSingleton('brander_productbanners/banner')->load($bannerId);
                    Mage::dispatchEvent(
                        'brander_productbanners_controller_banner_delete',
                        array('banner' => $banner)
                    );
                    $banner->delete();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('brander_productbanners')->__('Total of %d record(s) have been deleted.', count($bannerIds))
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
        $bannerIds = $this->getRequest()->getParam('banner');
        if (!is_array($bannerIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('brander_productbanners')->__('Please select banners.')
            );
        } else {
            try {
                foreach ($bannerIds as $bannerId) {
                $banner = Mage::getSingleton('brander_productbanners/banner')->load($bannerId)
                    ->setStatus($this->getRequest()->getParam('status'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d banners were successfully updated.', count($bannerIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('brander_productbanners')->__('There was an error updating banners.')
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
        return Mage::getSingleton('admin/session')->isAllowed('brander_shop/shop_content/brander_productbanners');
    }

    /**
     * Export banners in CSV format
     *
     * @access public
     * @return void

     */
    public function exportCsvAction()
    {
        $fileName   = 'banners.csv';
        $content    = $this->getLayout()->createBlock('brander_productbanners/adminhtml_banner_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export banners in Excel format
     *
     * @access public
     * @return void

     */
    public function exportExcelAction()
    {
        $fileName   = 'banner.xls';
        $content    = $this->getLayout()->createBlock('brander_productbanners/adminhtml_banner_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export banners in XML format
     *
     * @access public
     * @return void

     */
    public function exportXmlAction()
    {
        $fileName   = 'banner.xml';
        $content    = $this->getLayout()->createBlock('brander_productbanners/adminhtml_banner_grid')
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
            'brander_productbanners/adminhtml_productbanners_helper_form_wysiwyg_content',
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
     * get grid of products action
     *
     * @access public
     * @return void

     */
    public function productsAction()
    {
        $this->_initBanner();
        $this->loadLayout();
        $this->getLayout()->getBlock('banner.edit.tab.product')
            ->setBannerProducts($this->getRequest()->getPost('banner_products', null));
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
        $this->_initBanner();
        $this->loadLayout();
        $this->getLayout()->getBlock('banner.edit.tab.product')
            ->setBannerProducts($this->getRequest()->getPost('banner_products', null));
        $this->renderLayout();
    }
}
