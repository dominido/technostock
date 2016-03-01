<?php

class Brander_LayeredNavigation_Helper_Cached extends Mage_Core_Helper_Abstract
{
    const CACHE_TAG = 'brander_layerednavigation';

    private $lightCache = array();

    public function invalidateCache()
    {
        Mage::app()->getCacheInstance()->invalidateType('brander_layerednavigation');
    }

    protected function load($key)
    {
        if (array_key_exists($key, $this->lightCache)) {
            return $this->lightCache[$key];
        }

        if ($this->isCacheEnabled()) {
            $data = $this->_loadCache($this->makeCacheKey($key));
            if ($data === false) {
                return false;
            }

            $data = unserialize($data);

            $this->lightCache[$key] = $data;
            return $data;
        } else {
            return false;
        }
    }

    protected function save($data, $key)
    {
        $this->saveLite($data, $key);

        if ($this->isCacheEnabled()) {
            $this->_saveCache(serialize($data), $this->makeCacheKey($key), array(self::CACHE_TAG));
        }
    }

    protected function saveLite($data, $key)
    {
        $this->lightCache[$key] = $data;
    }

    private function makeCacheKey($key)
    {
        $storeId = Mage::app()->getStore()->getId();

        $isSearch = Mage::app()->getRequest()->getModuleName() == 'catalogsearch' ? 'search' : 'catalog';

        return 'layerednavigation_ ' . $isSearch . '_store' . $storeId . '_' . $key;
    }

    private function isCacheEnabled() {
        return Mage::app()->useCache(self::CACHE_TAG);
    }
}