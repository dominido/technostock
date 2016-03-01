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
class Brander_UnitopBlog_Model_Post extends Mage_Catalog_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'brander_unitopblog_post';
    const CACHE_TAG = 'brander_unitopblog_post';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'brander_unitopblog_post';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'post';
    protected $_productInstance = null;
    protected $_categoryInstance = null;

    /**
     * constructor
     *
     * @access public
     * @return void

     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('brander_unitopblog/post');
    }

    /**
     * before save post
     *
     * @access protected
     * @return Brander_UnitopBlog_Model_Post

     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * get the url to the post details page
     *
     * @access public
     * @return string

     */
    public function getPostUrl()
    {
        if ($this->getUrlKey()) {
            $urlKey = '';
            if ($prefix = Mage::getStoreConfig('brander_unitopblog/post/url_prefix')) {
                $urlKey .= $prefix.'/';
            }
            $urlKey .= $this->getUrlKey();
            if ($suffix = Mage::getStoreConfig('brander_unitopblog/post/url_suffix')) {
                $urlKey .= '.'.$suffix;
            }
            return Mage::getUrl('', array('_direct'=>$urlKey));
        }
        return Mage::getUrl('brander_unitopblog/post/view', array('id'=>$this->getId()));
    }

    /**
     * check URL key
     *
     * @access public
     * @param string $urlKey
     * @param bool $active
     * @return mixed

     */
    public function checkUrlKey($urlKey, $active = true)
    {
        return $this->_getResource()->checkUrlKey($urlKey, $active);
    }

    /**
     * get the post Preview Content
     *
     * @access public
     * @return string

     */
    public function getPreviewContent()
    {
        $preview_content = $this->getData('preview_content');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($preview_content);
        return $html;
    }

    /**
     * get the post Post Content
     *
     * @access public
     * @return string

     */
    public function getPostContent()
    {
        $post_content = $this->getData('post_content');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($post_content);
        return $html;
    }

    /**
     * save post relation
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Post

     */
    protected function _afterSave()
    {
        $this->getProductInstance()->savePostRelation($this);
        $this->getCategoryInstance()->savePostRelation($this);
        return parent::_afterSave();
    }

    /**
     * get product relation model
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Post_Product

     */
    public function getProductInstance()
    {
        if (!$this->_productInstance) {
            $this->_productInstance = Mage::getSingleton('brander_unitopblog/post_product');
        }
        return $this->_productInstance;
    }

    /**
     * get selected products array
     *
     * @access public
     * @return array

     */
    public function getSelectedProducts()
    {
        if (!$this->hasSelectedProducts()) {
            $products = array();
            foreach ($this->getSelectedProductsCollection() as $product) {
                $products[] = $product;
            }
            $this->setSelectedProducts($products);
        }
        return $this->getData('selected_products');
    }

    /**
     * Retrieve collection selected products
     *
     * @access public
     * @return Brander_UnitopBlog_Resource_Post_Product_Collection

     */
    public function getSelectedProductsCollection()
    {
        $collection = $this->getProductInstance()->getProductCollection($this);
        return $collection;
    }

    /**
     * get category relation model
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Post_Category

     */
    public function getCategoryInstance()
    {
        if (!$this->_categoryInstance) {
            $this->_categoryInstance = Mage::getSingleton('brander_unitopblog/post_category');
        }
        return $this->_categoryInstance;
    }

    /**
     * get selected categories array
     *
     * @access public
     * @return array

     */
    public function getSelectedCategories()
    {
        if (!$this->hasSelectedCategories()) {
            $categories = array();
            foreach ($this->getSelectedCategoriesCollection() as $category) {
                $categories[] = $category;
            }
            $this->setSelectedCategories($categories);
        }
        return $this->getData('selected_categories');
    }

    /**
     * Retrieve collection selected categories
     *
     * @access public
     * @return Brander_UnitopBlog_Resource_Post_Category_Collection

     */
    public function getSelectedCategoriesCollection()
    {
        $collection = $this->getCategoryInstance()->getCategoryCollection($this);
        return $collection;
    }

    /**
     * Retrieve parent 
     *
     * @access public
     * @return null|Brander_UnitopBlog_Model_Postscategory

     */
    public function getParentPostscategory()
    {
        if (!$this->hasData('_parent_postscategory')) {
            if (!$this->getPostscategoryId()) {
                return null;
            } else {
                $postscategory = Mage::getModel('brander_unitopblog/postscategory')->setStoreId(Mage::app()->getStore()->getId())
                    ->load($this->getPostscategoryId());
                if ($postscategory->getId()) {
                    $this->setData('_parent_postscategory', $postscategory);
                } else {
                    $this->setData('_parent_postscategory', null);
                }
            }
        }
        return $this->getData('_parent_postscategory');
    }

    /**
     * Retrieve default attribute set id
     *
     * @access public
     * @return int

     */
    public function getDefaultAttributeSetId()
    {
        return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }

    /**
     * get attribute text value
     *
     * @access public
     * @param $attributeCode
     * @return string

     */
    public function getAttributeText($attributeCode)
    {
        $text = $this->getResource()
            ->getAttribute($attributeCode)
            ->getSource()
            ->getOptionText($this->getData($attributeCode));
        if (is_array($text)) {
            return implode(', ', $text);
        }
        return $text;
    }

    /**
     * check if comments are allowed
     *
     * @access public
     * @return array

     */
    public function getAllowComments()
    {
        if ($this->getData('allow_comment') == Brander_UnitopBlog_Model_Adminhtml_Source_Yesnodefault::NO) {
            return false;
        }
        if ($this->getData('allow_comment') == Brander_UnitopBlog_Model_Adminhtml_Source_Yesnodefault::YES) {
            return true;
        }
        return Mage::getStoreConfigFlag('brander_unitopblog/post/allow_comment');
    }

    /**
     * get default values
     *
     * @access public
     * @return array

     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        $values['in_rss'] = 1;
        $values['allow_comment'] = Brander_UnitopBlog_Model_Adminhtml_Source_Yesnodefault::USE_DEFAULT;
        return $values;
    }
    
}
