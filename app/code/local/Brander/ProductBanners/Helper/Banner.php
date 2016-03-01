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
class Brander_ProductBanners_Helper_Banner extends Mage_Core_Helper_Abstract
{

    /**
     * check if breadcrumbs can be used
     *
     * @access public
     * @return bool

     */
    public function getUseBreadcrumbs()
    {
        return Mage::getStoreConfigFlag('brander_productbanners/banner/breadcrumbs');
    }

    /**
     * get base files dir
     *
     * @access public
     * @return string

     */
    public function getFileBaseDir()
    {
        return Mage::getBaseDir('media').DS.'banner'.DS.'file';
    }

    /**
     * get base file url
     *
     * @access public
     * @return string

     */
    public function getFileBaseUrl()
    {
        return Mage::getBaseUrl('media').'banner'.'/'.'file';
    }

    /**
     * get banner attribute source model
     *
     * @access public
     * @param string $inputType
     * @return mixed (string|null)

     */
     public function getAttributeSourceModelByInputType($inputType)
     {
         $inputTypes = $this->getAttributeInputTypes();
         if (!empty($inputTypes[$inputType]['source_model'])) {
             return $inputTypes[$inputType]['source_model'];
         }
         return null;
     }

    /**
     * get attribute input types
     *
     * @access public
     * @param string $inputType
     * @return array()

     */
    public function getAttributeInputTypes($inputType = null)
    {
        $inputTypes = array(
            'multiselect' => array(
                'backend_model' => 'eav/entity_attribute_backend_array'
            ),
            'boolean'     => array(
                'source_model'  => 'eav/entity_attribute_source_boolean'
            ),
            'file'          => array(
                'backend_model' => 'brander_productbanners/banner_attribute_backend_file'
            ),
            'image'          => array(
                'backend_model' => 'brander_productbanners/banner_attribute_backend_image'
            ),
        );

        if (is_null($inputType)) {
            return $inputTypes;
        } else if (isset($inputTypes[$inputType])) {
            return $inputTypes[$inputType];
        }
        return array();
    }

    /**
     * get banner attribute backend model
     *
     * @access public
     * @param string $inputType
     * @return mixed (string|null)

     */
    public function getAttributeBackendModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }
        return null;
    }

    /**
     * filter attribute content
     *
     * @access public
     * @param Brander_ProductBanners_Model_Banner $banner
     * @param string $attributeHtml
     * @param string @attributeName
     * @return string

     */
    public function bannerAttribute($banner, $attributeHtml, $attributeName)
    {
        $attribute = Mage::getSingleton('eav/config')->getAttribute(
            Brander_ProductBanners_Model_Banner::ENTITY,
            $attributeName
        );
        if ($attribute && $attribute->getId() && !$attribute->getIsWysiwygEnabled()) {
            if ($attribute->getFrontendInput() == 'textarea') {
                $attributeHtml = nl2br($attributeHtml);
            }
        }
        if ($attribute->getIsWysiwygEnabled()) {
            $attributeHtml = $this->_getTemplateProcessor()->filter($attributeHtml);
        }
        return $attributeHtml;
    }

    /**
     * get the template processor
     *
     * @access protected
     * @return Mage_Catalog_Model_Template_Filter

     */
    protected function _getTemplateProcessor()
    {
        if (null === $this->_templateProcessor) {
            $this->_templateProcessor = Mage::helper('catalog')->getPageTemplateProcessor();
        }
        return $this->_templateProcessor;
    }
}
