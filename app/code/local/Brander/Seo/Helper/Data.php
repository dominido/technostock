<?php

class Brander_Seo_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getNoIndexStores() {
		return (Mage::getStoreConfig('catalog/seo/stores_noindex')) ? explode(',',Mage::getStoreConfig('catalog/seo/stores_noindex')) : array();
	}

	public function checkCurrentStoreIsNoIndex() {
		$noIndexStores = Mage::helper('brander_seo')->getNoIndexStores();
		if(count($noIndexStores) && in_array(Mage::app()->getStore()->getCode(), $noIndexStores)) {
			return true;
		}

	}

    public function checkParam($params) {
        foreach ($params as $param) {
            if (strripos($param, "_")){
                return true;
            };
        }
        return false;
    }

}