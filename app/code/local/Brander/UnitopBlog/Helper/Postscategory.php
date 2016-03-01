<?php
/**
 * Brander UnitopBlog extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        UnitopBlog
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_UnitopBlog_Helper_Postscategory extends Mage_Core_Helper_Abstract
{

    static $_activeCategoryId = null;
    static $_activeCategoryTitle = null;

    /**
     * get the url to the post categories list page
     *
     * @access public
     * @return string

     */
    public function getPostscategoriesUrl()
    {
        if ($listKey = Mage::getStoreConfig('brander_unitopblog/postscategory/url_rewrite_list')) {
            return Mage::getUrl('', array('_direct'=>$listKey));
        }
        return Mage::getUrl('brander_unitopblog/postscategory/index');
    }

    public function getPostscategoriesSeoUrl($category)
    {
        $params = array('blog_category' => $category->getUrlKey());

        if ($listKey = Mage::getStoreConfig('brander_unitopblog/postscategory/url_rewrite_list')) {
            return Mage::getUrl('', array('_direct'=>$listKey, '_query' => $params));
        }
        return Mage::getUrl('brander_unitopblog/postscategory/index');
    }

    /**
     * check if breadcrumbs can be used
     *
     * @access public
     * @return bool

     */
    public function getUseBreadcrumbs()
    {
        return Mage::getStoreConfigFlag('brander_unitopblog/postscategory/breadcrumbs');
    }
    const POSTSCATEGORY_ROOT_ID = 1;
    /**
     * get the root id
     *
     * @access public
     * @return int

     */
    public function getRootPostscategoryId()
    {
        return self::POSTSCATEGORY_ROOT_ID;
    }

    /**
     * get base files dir
     *
     * @access public
     * @return string

     */
    public function getFileBaseDir()
    {
        return Mage::getBaseDir('media').DS.'postscategory'.DS.'file';
    }

    /**
     * get base file url
     *
     * @access public
     * @return string

     */
    public function getFileBaseUrl()
    {
        return Mage::getBaseUrl('media').'postscategory'.'/'.'file';
    }

    /**
     * get postscategory attribute source model
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
                'backend_model' => 'brander_unitopblog/postscategory_attribute_backend_file'
            ),
            'image'          => array(
                'backend_model' => 'brander_unitopblog/postscategory_attribute_backend_image'
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
     * get postscategory attribute backend model
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
     * @param Brander_UnitopBlog_Model_Postscategory $postscategory
     * @param string $attributeHtml
     * @param string @attributeName
     * @return string

     */
    public function postscategoryAttribute($postscategory, $attributeHtml, $attributeName)
    {
        $attribute = Mage::getSingleton('eav/config')->getAttribute(
            Brander_UnitopBlog_Model_Postscategory::ENTITY,
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

    public function getActiveBlogCategory($urlKey)
    {

        $category = Mage::getModel('brander_unitopblog/postscategory')
            ->getCollection()
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addAttributeToSelect('title')
            ->addAttributeToFilter('url_key', $urlKey)
            ->getFirstItem();

        if ($category->getEntityId()) {
            self::$_activeCategoryId = $category->getEntityId();
            self::$_activeCategoryTitle = $category->getTitle();
        }
        return $category;

    }

    public function getActiveCategoryId()
    {
        return self::$_activeCategoryId;
    }

    public function getActiveCategoryTitle()
    {
        return self::$_activeCategoryTitle;
    }

    public function getBlogPageTitle()
    {
        if ($title = $this->getActiveCategoryTitle()) {
        } elseif ($title = Mage::getStoreConfig('brander_unitopblog/postscategory/meta_title')) {
        } else {
            $title = Mage::helper('brander_unitopblog')->__('Blog');
        }
        return $title;
    }

    public function getBlogCrumb()
    {
        if ($title = Mage::getStoreConfig('brander_unitopblog/postscategory/meta_title')) {
        } else {
            $title = Mage::helper('brander_unitopblog')->__('Blog');
        }
        return $title;
    }

    public function isBlogSubcategory()
    {
        if ($this->getActiveCategoryTitle()) {
            return true;
        }
        return false;
    }
}
