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
class Brander_UnitopBlog_Block_Adminhtml_Postscategory_Tree extends Brander_UnitopBlog_Block_Adminhtml_Postscategory_Abstract
{
    /**
     * constructor
     *
     * @access public
     * @return void

     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('brander_unitopblog/postscategory/tree.phtml');
        $this->setUseAjax(true);
        $this->_withProductCount = true;
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Postscategory_Tree

     */
    protected function _prepareLayout()
    {
        $addUrl = $this->getUrl(
            "*/*/add",
            array(
                '_current'=>true,
                'id'=>null,
                '_query' => false
            )
        );

        $this->setChild(
            'add_sub_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('brander_unitopblog')->__('Add Child Post Category'),
                        'onclick' => "addNew('".$addUrl."', false)",
                        'class'   => 'add',
                        'id'      => 'add_child_postscategory_button',
                        'style'   => $this->canAddChild() ? '' : 'display: none;'
                    )
                )
        );

        $this->setChild(
            'add_root_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('brander_unitopblog')->__('Add Root Post Category'),
                        'onclick' => "addNew('".$addUrl."', true)",
                        'class'   => 'add',
                        'id'      => 'add_root_postscategory_button'
                    )
                )
        );
        $this->setChild(
            'store_switcher',
            $this->getLayout()->createBlock('adminhtml/store_switcher')
                ->setSwitchUrl(
                    $this->getUrl(
                        '*/*/*',
                        array(
                            '_current' =>true,
                            '_query'=>false,
                            'store'=>null
                        )
                    )
                )
                ->setTemplate('store/switcher/enhanced.phtml')
        );
        return parent::_prepareLayout();
    }

    /**
     * get the post category collection
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Collection

     */
    public function getPostscategoryCollection()
    {
        $collection = $this->getData('postscategory_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('brander_unitopblog/postscategory')->getCollection();
            $collection->addAttributeToSelect('title')->addAttributeToSelect('status');
            $this->setData('postscategory_collection', $collection);
        }
        return $collection;
    }

    /**
     * get html for add root button
     *
     * @access public
     * @return string

     */
    public function getAddRootButtonHtml()
    {
        return $this->getChildHtml('add_root_button');
    }

    /**
     * get html for add child button
     *
     * @access public
     * @return string

     */
    public function getAddSubButtonHtml()
    {
        return $this->getChildHtml('add_sub_button');
    }

    /**
     * get html for expand button
     *
     * @access public
     * @return string

     */
    public function getExpandButtonHtml()
    {
        return $this->getChildHtml('expand_button');
    }

    /**
     * get html for add collapse button
     *
     * @access public
     * @return string

     */
    public function getCollapseButtonHtml()
    {
        return $this->getChildHtml('collapse_button');
    }

    /**
     * get url for tree load
     *
     * @access public
     * @param mxed $expanded
     * @return string

     */
    public function getLoadTreeUrl($expanded=null)
    {
        $params = array('_current' => true, 'id' => null, 'store' => null);
        if ((is_null($expanded) &&
            Mage::getSingleton('admin/session')->getPostscategoryIsTreeWasExpanded()) ||
            $expanded == true) {
            $params['expand_all'] = true;
        }
        return $this->getUrl('*/*/postscategoriesJson', $params);
    }

    /**
     * get url for loading nodes
     *
     * @access public
     * @return string

     */
    public function getNodesUrl()
    {
        return $this->getUrl('*/unitopblog_postscategories/jsonTree');
    }

    /**
     * check if tree is expanded
     *
     * @access public
     * @return string

     */
    public function getIsWasExpanded()
    {
        return Mage::getSingleton('admin/session')->getPostscategoryIsTreeWasExpanded();
    }

    /**
     * get url for moving post category
     *
     * @access public
     * @return string

     */
    public function getMoveUrl()
    {
        return $this->getUrl('*/unitopblog_postscategory/move');
    }

    /**
     * get the tree as json
     *
     * @access public
     * @param mixed $parentNodePostscategory
     * @return string

     */
    public function getTree($parentNodePostscategory = null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parentNodePostscategory));
        $tree = isset($rootArray['children']) ? $rootArray['children'] : array();
        return $tree;
    }

    /**
     * get the tree as json
     *
     * @access public
     * @param mixed $parentNodePostscategory
     * @return string

     */
    public function getTreeJson($parentNodePostscategory = null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parentNodePostscategory));
        $json = Mage::helper('core')->jsonEncode(isset($rootArray['children']) ? $rootArray['children'] : array());
        return $json;
    }

    /**
     * Get JSON of array of post categories, that are breadcrumbs for specified post category path
     *
     * @access public
     * @param string $path
     * @param string $javascriptVarName
     * @return string

     */
    public function getBreadcrumbsJavascript($path, $javascriptVarName)
    {
        if (empty($path)) {
            return '';
        }

        $postscategories = Mage::getResourceSingleton('brander_unitopblog/postscategory_tree')
            ->loadBreadcrumbsArray($path);
        if (empty($postscategories)) {
            return '';
        }
        foreach ($postscategories as $key => $postscategory) {
            $postscategories[$key] = $this->_getNodeJson($postscategory);
        }
        return
            '<script type="text/javascript">'
            . $javascriptVarName . ' = ' . Mage::helper('core')->jsonEncode($postscategories) . ';'
            . ($this->canAddChild() ? '$("add_child_postscategory_button").show();' : '$("add_child_postscategory_button").hide();')
            . '</script>';
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @access protected
     * @param Varien_Data_Tree_Node|array $node
     * @param int $level
     * @return string

     */
    protected function _getNodeJson($node, $level = 0)
    {
        // create a node from data array
        if (is_array($node)) {
            $node = new Varien_Data_Tree_Node($node, 'entity_id', new Varien_Data_Tree);
        }
        $item = array();
        $item['text'] = $this->buildNodeName($node);
        $item['id']   = $node->getId();
        $item['path'] = $node->getData('path');
        $item['cls']  = 'folder';
        if ($node->getStatus()) {
            $item['cls'] .= ' active-category';
        } else {
            $item['cls'] .= ' no-active-category';
        }
        $item['allowDrop'] = true;
        $item['allowDrag'] = true;
        if ((int)$node->getChildrenCount()>0) {
            $item['children'] = array();
        }
        $isParent = $this->_isParentSelectedPostscategory($node);
        if ($node->hasChildren()) {
            $item['children'] = array();
            if (!($this->getUseAjax() && $node->getLevel() > 1 && !$isParent)) {
                foreach ($node->getChildren() as $child) {
                    $item['children'][] = $this->_getNodeJson($child, $level+1);
                }
            }
        }
        if ($isParent || $node->getLevel() < 1) {
            $item['expanded'] = true;
        }
        return $item;
    }

    /**
     * Get node label
     *
     * @access public
     * @param Varien_Object $node
     * @return string

     */
    public function buildNodeName($node)
    {
        $result = $this->escapeHtml($node->getTitle());
        return $result;
    }

    /**
     * check if entity is movable
     *
     * @access protected
     * @param Varien_Object $node
     * @return bool

     */
    protected function _isPostscategoryMoveable($node)
    {
        return true;
    }

    /**
     * check if parent is selected
     *
     * @access protected
     * @param Varien_Object $node
     * @return bool

     */
    protected function _isParentSelectedPostscategory($node)
    {
        if ($node && $this->getPostscategory()) {
            $pathIds = $this->getPostscategory()->getPathIds();
            if (in_array($node->getId(), $pathIds)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if page loaded by outside link to post category edit
     *
     * @access public
     * @return boolean

     */
    public function isClearEdit()
    {
        return (bool) $this->getRequest()->getParam('clear');
    }

    /**
     * Check availability of adding root post category
     *
     * @access public
     * @return boolean

     */
    public function canAddRootPostscategory()
    {
        return true;
    }

    /**
     * Check availability of adding child post category
     *
     * @access public
     * @return boolean
     */
    public function canAddChild()
    {
        return true;
    }

    /**
     * get store switcher html
     *
     * @access public
     * @return string

     */
    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    /**
     * get current store
     *
     * @access public
     * @return string

     */
    public function getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        return Mage::app()->getStore($storeId);
    }

    /**
     * get switch url
     *
     * @access public
     * @return string

     */
    public function getSwitchTreeUrl()
    {
        return $this->getUrl(
            "*/unitopblog_postscategory/tree",
            array(
                '_current'=>true,
                'store'=>null,
                '_query'=>false,
                'id'=>null,
                'parent'=>null
            )
        );
    }

}
