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
class Brander_ProductBanners_BannerController extends Mage_Core_Controller_Front_Action
{

    /**
     * init Banner
     *
     * @access protected
     * @return Brander_ProductBanners_Model_Banner

     */
    protected function _initBanner()
    {
        $bannerId   = $this->getRequest()->getParam('id', 0);
        $banner     = Mage::getModel('brander_productbanners/banner')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($bannerId);
        if (!$banner->getId()) {
            return false;
        } elseif (!$banner->getStatus()) {
            return false;
        }
        return $banner;
    }

    /**
     * view banner action
     *
     * @access public
     * @return void

     */
    public function viewAction()
    {
        $banner = $this->_initBanner();
        if (!$banner) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_banner', $banner);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('productbanners-banner productbanners-banner' . $banner->getId());
        }
        if (Mage::helper('brander_productbanners/banner')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('brander_productbanners')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'banner',
                    array(
                        'label' => $banner->getTitle(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $banner->getBannerUrl());
        }
        $this->renderLayout();
    }
}
