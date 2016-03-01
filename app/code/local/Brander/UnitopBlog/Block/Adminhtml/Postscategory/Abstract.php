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
class Brander_UnitopBlog_Block_Adminhtml_Postscategory_Abstract extends Mage_Adminhtml_Block_Template
{
    /**
     * get current post category
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Entity

     */
    public function getPostscategory()
    {
        return Mage::registry('postscategory');
    }

    /**
     * get current post category id
     *
     * @access public
     * @return int

     */
    public function getPostscategoryId()
    {
        if ($this->getPostscategory()) {
            return $this->getPostscategory()->getId();
        }
        return null;
    }

    /**
     * get current post category Title
     *
     * @access public
     * @return string

     */
    public function getPostscategoryTitle()
    {
        return $this->getPostscategory()->getTitle();
    }

    /**
     * get current post category path
     *
     * @access public
     * @return string

     */
    public function getPostscategoryPath()
    {
        if ($this->getPostscategory()) {
            return $this->getPostscategory()->getPath();
        }
        return Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId();
    }

    /**
     * check if there is a root post category
     *
     * @access public
     * @return bool

     */
    public function hasRootPostscategory()
    {
        $root = $this->getRoot();
        if ($root && $root->getId()) {
            return true;
        }
        return false;
    }

    /**
     * get the root
     *
     * @access public
     * @param Brander_UnitopBlog_Model_Postscategory|null $parentNodePostscategory
     * @param int $recursionLevel
     * @return Varien_Data_Tree_Node

     */
    public function getRoot($parentNodePostscategory = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodePostscategory) && $parentNodePostscategory->getId()) {
            return $this->getNode($parentNodePostscategory, $recursionLevel);
        }
        $root = Mage::registry('root');
        if (is_null($root)) {
            $rootId = Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId();
            $tree = Mage::getResourceSingleton('brander_unitopblog/postscategory_tree')
                ->load(null, $recursionLevel);
            if ($this->getPostscategory()) {
                $tree->loadEnsuredNodes($this->getPostscategory(), $tree->getNodeById($rootId));
            }
            $tree->addCollectionData($this->getPostscategoryCollection());
            $root = $tree->getNodeById($rootId);
            if ($root && $rootId != Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId()) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId()) {
                $root->setTitle(Mage::helper('brander_unitopblog')->__('Root'));
            }
            Mage::register('root', $root);
        }
        return $root;
    }

    /**
     * Get and register post categories root by specified post categories IDs
     *
     * @accsess public
     * @param array $ids
     * @return Varien_Data_Tree_Node

     */
    public function getRootByIds($ids)
    {
        $root = Mage::registry('root');
        if (null === $root) {
            $postscategoryTreeResource = Mage::getResourceSingleton('brander_unitopblog/postscategory_tree');
            $ids     = $postscategoryTreeResource->getExistingPostscategoryIdsBySpecifiedIds($ids);
            $tree   = $postscategoryTreeResource->loadByIds($ids);
            $rootId = Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId();
            $root   = $tree->getNodeById($rootId);
            if ($root && $rootId != Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId()) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId()) {
                $root->setName(Mage::helper('brander_unitopblog')->__('Root'));
            }
            $tree->addCollectionData($this->getPostscategoryCollection());
            Mage::register('root', $root);
        }
        return $root;
    }

    /**
     * get specific node
     *
     * @access public
     * @param Brander_UnitopBlog_Model_Postscategory $parentNodePostscategory
     * @param $int $recursionLevel
     * @return Varien_Data_Tree_Node

     */
    public function getNode($parentNodePostscategory, $recursionLevel = 2)
    {
        $tree = Mage::getResourceModel('brander_unitopblog/postscategory_tree');
        $nodeId     = $parentNodePostscategory->getId();
        $parentId   = $parentNodePostscategory->getParentId();
        $node = $tree->loadNode($nodeId);
        $node->loadChildren($recursionLevel);
        if ($node && $nodeId != Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId()) {
            $node->setIsVisible(true);
        } elseif ($node && $node->getId() == Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId()) {
            $node->setTitle(Mage::helper('brander_unitopblog')->__('Root'));
        }
        $tree->addCollectionData($this->getPostscategoryCollection());
        return $node;
    }

    /**
     * get url for saving data
     *
     * @access public
     * @param array $args
     * @return string

     */
    public function getSaveUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/save', $params);
    }

    /**
     * get url for edit
     *
     * @access public
     * @param array $args
     * @return string

     */
    public function getEditUrl()
    {
        return $this->getUrl(
            "*/unitopblog_postscategory/edit",
            array('_current' => true, '_query'=>false, 'id' => null, 'parent' => null)
        );
    }

    /**
     * Return root ids
     *
     * @access public
     * @return array

     */
    public function getRootIds()
    {
        return array(Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId());
    }
}
