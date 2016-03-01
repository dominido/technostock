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
class ET_SocialLogin_Helper_Data extends Mage_Core_Helper_Abstract
{

    static $storeId = null;

    public function getActiveSocialAccounts()
    {
        $config = Mage::getStoreConfig('social_login');

        $config = array_filter($config, function ($item) {
            if (!isset($item['social']) || !isset($item['key']) || !isset($item['secret'])
                || empty($item['key']) || empty($item['secret'])
            ) {
                return null;
            }

            return !isset($item['active']) ? false : ($item['active'] == 1);
        });

        uasort($config, array($this, 'socialSort'));

        return $config;
    }

    public function socialSort($a, $b)
    {
        if ((int)$a['sort_order'] == (int)$b['sort_order']) {
            return 0;
        }
        return ((int)$a['sort_order'] < (int)$b['sort_order']) ? -1 : 1;
    }


    /**
     * Retrieve current url with port from store base url
     *
     * @return string
     */
    public function getCurrentUrlWithStorePort()
    {
        $isStoreSecure = Mage::app()->getStore()->isCurrentlySecure();
        $baseUrl = ($isStoreSecure) ? Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, TRUE)
            : Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $storePort = parse_url($baseUrl, PHP_URL_PORT);

        $request = Mage::app()->getRequest();
        $url = $request->getScheme()
            . '://' . $request->getHttpHost()
            . ($storePort ? ':' . $storePort : '')
            . $request->getServer('REQUEST_URI');

        $url = Mage::getSingleton('core/url')->escape($url);

        return $url;
    }

    public function getSocialIconsClass($storeId, $type = 'auth')
    {
        switch ($type) {
            case 'auth':
                $shape = Mage::getStoreConfig('social_login/icons/shape', $storeId);
                $color = Mage::getStoreConfig('social_login/icons/color', $storeId);
                break;
            case 'share':
                $shape = Mage::getStoreConfig('social_login/sharing/shape', $storeId);
                $color = Mage::getStoreConfig('social_login/sharing/color', $storeId);
                break;
            default:
                $shape = Mage::getStoreConfig('social_login/icons/shape', $storeId);
                $color = Mage::getStoreConfig('social_login/icons/color', $storeId);
        }

        return $shape . '-' . $color;
    }

    public function getSocialIconsSizeClass($storeId, $type = 'auth')
    {
        switch ($type) {
            case 'auth':
                $size = Mage::getStoreConfig('social_login/icons/size', $storeId);
                break;
            case 'share':
                $size = Mage::getStoreConfig('social_login/sharing/size', $storeId);
                break;
            default:
                $size = Mage::getStoreConfig('social_login/icons/size', $storeId);
        }

        if ($size == '24') {
            $size = 'small';
        } else {
            $size = 'large';
        }
        return 'socicons-' . $size;
    }

    public function getStoreLogo()
    {
        return Mage::getDesign()->getSkinUrl(Mage::getStoreConfig('design/header/logo_src'));
    }

    /**
     * Retrieve Current Store Id
     *
     * @return int|null
     */
    public function getStoreId()
    {
        if (self::$storeId == null) {
            self::$storeId = Mage::app()->getStore()->getId();
        }
        return self::$storeId;
    }


    public function isSocialEnabled($storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->getStoreId();
        }

        return (int)Mage::getStoreConfig('social_login/general/active', $storeId);
    }

    public function isSocialEnabledOnLoginPage($storeId)
    {
        if (!$this->isSocialEnabled($storeId)) {
            return false;
        }
        return (int)Mage::getStoreConfig('social_login/general/login_page_active', $storeId);
    }

    public function isSocialEnabledOnCheckoutPage($storeId)
    {
        if (!$this->isSocialEnabled($storeId)) {
            return false;
        }
        return (int)Mage::getStoreConfig('social_login/general/checkout_page_active', $storeId);
    }

    public function isSharingEnabled($storeId = null)
    {
        if (!$this->isSocialEnabled($storeId)) {
            return false;
        }

        return Mage::getStoreConfig('social_login/sharing/sharing');
    }

    public function resizeImage($imageName, $width = NULL, $height = NULL, $imagePath = NULL)
    {
        $imagePath = str_replace("/", DS, $imagePath);
        $imagePathFull = Mage::getBaseDir('media') . DS . $imagePath . DS . $imageName;

        if ($width == NULL && $height == NULL) {
            $width = 100;
            $height = 100;
        }
        $resizePath = $width . 'x' . $height;
        $resizePathFull = Mage::getBaseDir('media') . DS . $imagePath . DS . $resizePath . DS . $imageName;

        if (file_exists($imagePathFull) && !file_exists($resizePathFull)) {
            $imageObj = new Varien_Image($imagePathFull);
            $imageObj->keepAspectRatio(TRUE);
            $imageObj->resize($width, $height);
            $imageObj->save($resizePathFull);
        }

        $imagePath = str_replace(DS, "/", $imagePath);
        return Mage::getBaseUrl("media") . $imagePath . "/" . $resizePath . "/" . $imageName;
    }

    /**
     * Checks if share option for this provider is enabled
     * @param $provider
     * @return mixed
     */
    public function isProviderEnabled($provider)
    {
        return Mage::getStoreConfig('social_login/sharing/' . $provider, self::$storeId);
    }

    /**
     * @param $provider
     * @return mixed
     */
    public function getShareIconTitle($provider)
    {
        return Mage::getStoreConfig('social_login/sharing/' . $provider . '_icon_title', self::$storeId);
    }
}