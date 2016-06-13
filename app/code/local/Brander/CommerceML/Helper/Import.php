<?php

class Brander_CommerceML_Helper_Import extends Mage_Core_Helper_Abstract
{
    public function getImportDir()
    {
        return Mage::getBaseDir('var') . DS . 'import';
    }

    public function getImportFilePath($file)
    {
        return $this->getImportDir() . DS . $file;
    }

    public function getMediaImportDir()
    {
        return Mage::getBaseDir('media') . DS . 'import';
    }

    public function log($message = null, $isError = true)
    {
        if ($message) {
            $filename = 'brandercml' . ($isError ? '_error' : '') . '.log';
            Mage::log($message, null, $filename, true);
        }
    }

    public function prepareAttributeCode($name, $code = null)
    {
        if ($code === null || $code === '') {
            $name = $this->prepareAttributeName($name);
            $code = Mage::helper('catalog/product_url')->format($name);
        }

        $code = preg_replace('/[^0-9a-z]/i', '', $code);
        return strtolower(substr($code, 0, 30));
    }

    public function prepareAttributeName($name)
    {
        if ($name = explode(',', $name)) {
            $name = $name[0];
        }
        return trim($name);
    }

    public function prepareAttributeFilterable($filterable = null)
    {
        switch ($filterable) {
            case 'withoutresult':
                return 2;
            case 'withresult': case 'true':
                return 1;
            default: case 'false':
                return 0;
        }
    }

    public function prepareProductMediaGallery($images, $string = true, $separator = ';', $prefix = '/')
    {
        $pregfilter = Mage::getStoreConfig(Brander_CommerceML_Model_Import::PROD_IMAGE_PATH_PREGFILTER);
        $replacewith = Mage::getStoreConfig(Brander_CommerceML_Model_Import::PROD_IMAGE_PATH_REPLACEWITH);
        $result = array();

        if (!is_string($images) && $images) {
            foreach ($images as $key => $image) {
                if (!$image = (string)$image) {
                    continue;
                }

                $result[$key] = $prefix . $image;

                if ($pregfilter && $replacewith) {
                    $result[$key] = preg_replace($pregfilter, $replacewith, $result[$key]);
                }
            }
        }

        if (count($result) && $string) {
            return implode($separator, $result);
        } else if (!count($result) && $string) {
            return '';
        } else {
            return $result;
        }
    }

    public function prepareProductImage($image)
    {
        $pregfilter     = Mage::getStoreConfig(Brander_CommerceML_Model_Import::PROD_IMAGE_PATH_PREGFILTER);
        $replacewith    = Mage::getStoreConfig(Brander_CommerceML_Model_Import::PROD_IMAGE_PATH_REPLACEWITH);
        $exclude        = Mage::getStoreConfig(Brander_CommerceML_Model_Import::EXCLUDE_BASE_IMAGE);

        if ($pregfilter && $replacewith) {
            $image = preg_replace($pregfilter, $replacewith, $image);
        }

        return $exclude ? $image : /*'+' . */$image;
    }

    public function prepareCategoryName($category)
    {
        if (is_array($category) && count($category)) {
            return implode('/', array_reverse($category));
        }
    }
}