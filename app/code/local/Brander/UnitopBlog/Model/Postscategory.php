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
class Brander_UnitopBlog_Model_Postscategory extends Mage_Catalog_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'brander_unitopblog_postscategory';
    const CACHE_TAG = 'brander_unitopblog_postscategory';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'brander_unitopblog_postscategory';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'postscategory';

    /**
     * constructor
     *
     * @access public
     * @return void

     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('brander_unitopblog/postscategory');
    }

    /**
     * before save post category
     *
     * @access protected
     * @return Brander_UnitopBlog_Model_Postscategory

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
     * get the url to the post category details page
     *
     * @access public
     * @return string

     */
    public function getPostscategoryUrl()
    {
        if ($this->getUrlKey()) {
            $urlKey = '';
            if ($prefix = Mage::getStoreConfig('brander_unitopblog/postscategory/url_prefix')) {
                $urlKey .= $prefix.'/';
            }
            $urlKey .= $this->getUrlKey();
            if ($suffix = Mage::getStoreConfig('brander_unitopblog/postscategory/url_suffix')) {
                $urlKey .= '.'.$suffix;
            }
            return Mage::getUrl('', array('_direct'=>$urlKey));
        }
        return Mage::getUrl('brander_unitopblog/postscategory/view', array('id'=>$this->getId()));
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
     * get the post category Short Description
     *
     * @access public
     * @return string

     */
    public function getShortDescription()
    {
        $short_description = $this->getData('short_description');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($short_description);
        return $html;
    }

    /**
     * get the post category Description
     *
     * @access public
     * @return string

     */
    public function getDescription()
    {
        $description = $this->getData('description');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($description);
        return $html;
    }

    /**
     * save post category relation
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Postscategory

     */
    protected function _afterSave()
    {
        return parent::_afterSave();
    }

    /**
     * Retrieve  collection
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Post_Collection

     */
    public function getSelectedPostsCollection()
    {
        if (!$this->hasData('_post_collection')) {
            if (!$this->getId()) {
                return new Varien_Data_Collection();
            } else {
                $collection = Mage::getResourceModel('brander_unitopblog/post_collection')->addAttributeToSelect('*')
                        ->addAttributeToFilter('postscategory_id', $this->getId());
                $this->setData('_post_collection', $collection);
            }
        }
        return $this->getData('_post_collection');
    }

    /**
     * get the tree model
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Tree

     */
    public function getTreeModel()
    {
        return Mage::getResourceModel('brander_unitopblog/postscategory_tree');
    }

    /**
     * get tree model instance
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Tree

     */
    public function getTreeModelInstance()
    {
        if (is_null($this->_treeModel)) {
            $this->_treeModel = Mage::getResourceSingleton('brander_unitopblog/postscategory_tree');
        }
        return $this->_treeModel;
    }

    /**
     * Move post category
     *
     * @access public
     * @param   int $parentId new parent post category id
     * @param   int $afterPostscategoryId post category id after which we have put current post category
     * @return  Brander_UnitopBlog_Model_Postscategory

     */
    public function move($parentId, $afterPostscategoryId)
    {
        $parent = Mage::getModel('brander_unitopblog/postscategory')->load($parentId);
        if (!$parent->getId()) {
            Mage::throwException(
                Mage::helper('brander_unitopblog')->__(
                    'Post Category move operation is not possible: the new parent post category was not found.'
                )
            );
        }
        if (!$this->getId()) {
            Mage::throwException(
                Mage::helper('brander_unitopblog')->__(
                    'Post Category move operation is not possible: the current post category was not found.'
                )
            );
        } elseif ($parent->getId() == $this->getId()) {
            Mage::throwException(
                Mage::helper('brander_unitopblog')->__(
                    'Post Category move operation is not possible: parent post category is equal to child post category.'
                )
            );
        }
        $this->setMovedPostscategoryId($this->getId());
        $eventParams = array(
            $this->_eventObject => $this,
            'parent'            => $parent,
            'postscategory_id'     => $this->getId(),
            'prev_parent_id'    => $this->getParentId(),
            'parent_id'         => $parentId
        );
        $moveComplete = false;
        $this->_getResource()->beginTransaction();
        try {
            $this->getResource()->changeParent($this, $parent, $afterPostscategoryId);
            $this->_getResource()->commit();
            $this->setAffectedPostscategoryIds(array($this->getId(), $this->getParentId(), $parentId));
            $moveComplete = true;
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        if ($moveComplete) {
            Mage::app()->cleanCache(array(self::CACHE_TAG));
        }
        return $this;
    }

    /**
     * Get the parent post category
     *
     * @access public
     * @return  Brander_UnitopBlog_Model_Postscategory

     */
    public function getParentPostscategory()
    {
        if (!$this->hasData('parent_postscategory')) {
            $this->setData(
                'parent_postscategory',
                Mage::getModel('brander_unitopblog/postscategory')->load($this->getParentId())
            );
        }
        return $this->_getData('parent_postscategory');
    }

    /**
     * Get the parent id
     *
     * @access public
     * @return  int

     */
    public function getParentId()
    {
        $parentIds = $this->getParentIds();
        return intval(array_pop($parentIds));
    }

    /**
     * Get all parent post categories ids
     *
     * @access public
     * @return array

     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), array($this->getId()));
    }

    /**
     * Get all post categories children
     *
     * @access public
     * @param bool $asArray
     * @return mixed (array|string)

     */
    public function getAllChildren($asArray = false)
    {
        $children = $this->getResource()->getAllChildren($this);
        if ($asArray) {
            return $children;
        } else {
            return implode(',', $children);
        }
    }

    /**
     * Get all post categories children
     *
     * @access public
     * @return string

     */
    public function getChildPostscategories()
    {
        return implode(',', $this->getResource()->getChildren($this, false));
    }

    /**
     * check the id
     *
     * @access public
     * @param int $id
     * @return bool

     */
    public function checkId($id)
    {
        return $this->_getResource()->checkId($id);
    }

    /**
     * Get array post categories ids which are part of post category path
     *
     * @access public
     * @return array

     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if (is_null($ids)) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }
        return $ids;
    }

    /**
     * Retrieve level
     *
     * @access public
     * @return int

     */
    public function getLevel()
    {
        if (!$this->hasLevel()) {
            return count(explode('/', $this->getPath())) - 1;
        }
        return $this->getData('level');
    }

    /**
     * Verify post category ids
     *
     * @access public
     * @param array $ids
     * @return bool

     */
    public function verifyIds(array $ids)
    {
        return $this->getResource()->verifyIds($ids);
    }

    /**
     * check if post category has children
     *
     * @access public
     * @return bool

     */
    public function hasChildren()
    {
        return $this->_getResource()->getChildrenAmount($this) > 0;
    }

    /**
     * check if post category can be deleted
     *
     * @access protected
     * @return Brander_UnitopBlog_Model_Postscategory

     */
    protected function _beforeDelete()
    {
        if ($this->getResource()->isForbiddenToDelete($this->getId())) {
            Mage::throwException(Mage::helper('brander_unitopblog')->__("Can't delete root post category."));
        }
        return parent::_beforeDelete();
    }

    /**
     * get the post categories
     *
     * @access public
     * @param Brander_UnitopBlog_Model_Postscategory $parent
     * @param int $recursionLevel
     * @param bool $sorted
     * @param bool $asCollection
     * @param bool $toLoad

     */
    public function getPostscategories($parent, $recursionLevel = 0, $sorted=false, $asCollection=false, $toLoad=true)
    {
        return $this->getResource()->getPostscategories($parent, $recursionLevel, $sorted, $asCollection, $toLoad);
    }

    /**
     * Return parent post categories of current post category
     *
     * @access public
     * @return array

     */
    public function getParentPostscategories()
    {
        return $this->getResource()->getParentPostscategories($this);
    }

    /**
     * Return children post categories of current post category
     *
     * @access public
     * @return array

     */
    public function getChildrenPostscategories()
    {
        return $this->getResource()->getChildrenPostscategories($this);
    }

    /**
     * check if parents are enabled
     *
     * @access public
     * @return bool

     */
    public function getStatusPath()
    {
        $parents = $this->getParentPostscategories();
        $rootId = Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId();
        foreach ($parents as $parent) {
            if ($parent->getId() == $rootId) {
                continue;
            }
            if (!$parent->getStatus()) {
                return false;
            }
        }
        return $this->getStatus();
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
     * get default values
     *
     * @access public
     * @return array

     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
    
}
