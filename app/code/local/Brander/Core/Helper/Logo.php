<?php

class Brander_Core_Helper_Logo extends Mage_Core_Helper_Abstract
{
    public function getHeaderLogo() {
        return Mage::getStoreConfig('design/header/custom_header_logo');
    }

    public function getFooterLogo() {
        return Mage::getStoreConfig('design/footer/custom_footer_logo');
    }

    public function resizeLogo($type, $width, $height) {
        switch ($type) {
        case 'header':
            $fileName = $this->getHeaderLogo();
            break;
        case 'footer':
            $fileName = $this->getFooterLogo();
            break;
        default:
            $fileName = $this->getHeaderLogo();
            break;
        }
        if ($fileName) {
            /** @var Brander_Core_Helper_Data $helper */
            $helper = Mage::helper('brander_core');
            return $helper->getResizedImage("logo","logo/cache",$fileName,$width . 'x' . $height . $fileName,$width,$height);
        }

        return '';
    }
}