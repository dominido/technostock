<?php

/**
 * NOTICE OF LICENSE
 *
 * You may not give, sell, distribute, sub-license, rent, lease or lend
 * any portion of the Software or Documentation to anyone.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_SocialLogin
 * @copyright  Copyright (c) 2013 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */
class ET_SocialLogin_Block_Meta extends Mage_Core_Block_Template
{

    public function __construct()
    {
        $storeId = Mage::app()->getStore()->getId();
        $helper = Mage::helper('et_sociallogin');
        if ($helper->isSocialEnabled($storeId)) {
            $this->setTemplate('et_sociallogin/meta.phtml');
        }
        parent::__construct();
    }

    public function _toHtml()
    {
        return parent::_toHtml();
    }

    public function getOpenGraphTags()
    {
        $helper = Mage::helper('et_sociallogin');
        if ($this->getRequest()->getControllerName() == 'product') {
            $product = Mage::registry('product');

            return array(
                'og:title' => $this->getLayout()->getBlock('head')->getTitle(),
                'og:type' => 'website',
                'og:url' => $helper->getCurrentUrlWithStorePort(),
                'og:image' => (isset($product) && $product->getId()) ? Mage::getModel('catalog/product_media_config')->getMediaUrl(Mage::registry('product')->getSmallImage())
                    : $helper->getStoreLogo(),
                'og:description' => htmlspecialchars(strip_tags((isset($product) && $product->getId())
                    ? htmlspecialchars(str_replace(array("'", "\r", "\n"), array("\'", " ", " "),
                        $product->getShortDescription()))
                    : $this->getLayout()->getBlock('head')->getTitle())),
            );
        } elseif ($this->getRequest()->getControllerName() == 'newsitem') {

            $newsItem = Mage::registry('newsitem');
            $image = ($newsItem->getImageFullContent()) ? $newsItem->getImageFullContent() : $helper->getStoreLogo();
            $helper->resizeImage(str_replace('clnews/', '',
                $newsItem->getImageFullContent()), '1200', '630', 'clnews');
            return array(
                'og:title' => $newsItem->getTitle(),
                'og:type' => 'website',
                'og:url' => $helper->getCurrentUrlWithStorePort(),
                'og:image' => $helper->resizeImage(str_replace('clnews/', '',
                        $image), '1200', '630', 'clnews'),
                'og:description' => htmlspecialchars(str_replace(array("'", "\r", "\n"), array("\'", " ", " "),
                    $newsItem->getShortContent()))
            );
        }
        return array();
    }

}