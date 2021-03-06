<?php
/**
 * Magento Commercial Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Commercial Edition License
 * that is available at: http://www.magentocommerce.com/license/commercial-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Categories tree block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Brander_Cms_Block_Adminhtml_Page_Tree extends Brander_Cms_Block_Adminhtml_Page_Abstract
{
    protected $_withProductCount;

    public function getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        return Mage::app()->getStore($storeId);
    }

    public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(true);
        $this->_withProductCount = true;
    }

    protected function _prepareLayout()
    {
        $addUrl = $this->getUrl("*/*/add", array(
            '_current' => true,
            'id' => null,
            '_query' => false
        ));

        $this->setChild('add_sub_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Add Subpage'),
                    'onclick'   => "addNew('".$addUrl."', false)",
                    'class'     => 'add',
                    'id'        => 'add_subcategory_button',
                    'style'     => $this->canAddSubCategory() ? '' : 'display: none;'
                ))
        );

        if ($this->canAddRootCategory()) {
            $this->setChild('add_root_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('catalog')->__('Add Root Page'),
                        'onclick'   => "addNew('".$addUrl."', true)",
                        'class'     => 'add',
                        'id'        => 'add_root_category_button'
                    ))
            );
        }

        $this->setChild('store_switcher',
            $this->getLayout()->createBlock('adminhtml/store_switcher')
                ->setSwitchUrl($this->getUrl('*/*/*', array('_current'=>true, '_query'=>false, 'store'=>null)))
                ->setTemplate('store/switcher/enhanced.phtml')
        );
        return parent::_prepareLayout();
    }

    protected function _getDefaultStoreId()
    {
        return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }

    public function getEditUrl()
    {
        return $this->getUrl("*/cms_advanced/edit", array('_current'=>true, 'store'=>null, '_query'=>false, 'id'=>null, 'parent'=>null));
    }

    public function getPageCollection()
    {
        $storeId = $this->getRequest()->getParam('store', $this->_getDefaultStoreId());
        $collection = $this->getData('page_collection');
 
        if (is_null($collection)) {
            $collection = Mage::getModel('cmsadvanced/page')->getCollection();
        
            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('is_active')
                ->setStoreId($storeId);
   
            $this->setData('page_collection', $collection);
        }
    
        return $collection;
    }

    public function getAddRootButtonHtml()
    {
        return $this->getChildHtml('add_root_button');
    }

    public function getAddSubButtonHtml()
    {
        return $this->getChildHtml('add_sub_button');
    }

    public function getExpandButtonHtml()
    {
        return $this->getChildHtml('expand_button');
    }

    public function getCollapseButtonHtml()
    {
        return $this->getChildHtml('collapse_button');
    }

    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    public function getLoadTreeUrl($expanded=null)
    {
        $params = array('_current'=>true, 'id'=>null,'store'=>null);
        if (
            (is_null($expanded) && Mage::getSingleton('admin/session')->getIsTreeWasExpanded())
            || $expanded == true) {
            $params['expand_all'] = true;
        }
        return $this->getUrl('*/*/pagesJson', $params);
    }

    public function getNodesUrl()
    {
        return $this->getUrl('*/catalog_category/jsonTree');
    }

    public function getSwitchTreeUrl()
    {
        return $this->getUrl("*/cms_advanced/tree", array('_current'=>true, 'store'=>null, '_query'=>false, 'id'=>null, 'parent'=>null));
    }

    public function getIsWasExpanded()
    {
        return Mage::getSingleton('admin/session')->getIsTreeWasExpanded();
    }

    public function getMoveUrl()
    {
        return $this->getUrl('*/*/move', array('store'=>$this->getRequest()->getParam('store')));
    }

    public function getTree($parenNodeCategory=null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parenNodeCategory));
        $tree = isset($rootArray['children']) ? $rootArray['children'] : array();
        return $tree;
    }

    public function getTreeJson($parenNodeCategory=null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parenNodeCategory));
        $json = Mage::helper('core')->jsonEncode(isset($rootArray['children']) ? $rootArray['children'] : array());
        return $json;
    }

    /**
     * Get JSON of array of categories, that are breadcrumbs for specified category path
     *
     * @param string $path
     * @param string $javascriptVarName
     * @return string
     */
    public function getBreadcrumbsJavascript($path, $javascriptVarName)
    {
        if (empty($path)) {
            return '';
        }

        $pages = Mage::getResourceSingleton('cmsadvanced/page_tree')
            ->setStoreId($this->getStore()->getId())->loadBreadcrumbsArray($path);

        if (empty($pages)) {
            return '';
        }
        foreach ($pages as $key => $page) {
            $pages[$key] = $this->_getNodeJson($page);
        }
        return
            '<script type="text/javascript">'
            . $javascriptVarName . ' = ' . Mage::helper('core')->jsonEncode($pages) . ';'
            . ($this->canAddSubCategory() ? '$("add_subcategory_button").show();' : '$("add_subcategory_button").hide();')
            . '</script>';
    }

    /**
     * Get JSON of a tree node or an associative array
     *
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

        $rootForStores = in_array($node->getEntityId(), $this->getRootIds());

        $item['id']  = $node->getId();
        $item['store']  = (int) $this->getStore()->getId();
        $item['path'] = $node->getData('path');

        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        $allowMove = $this->_isPageMoveable($node);
        $item['allowDrop'] = $allowMove;
        // disallow drag if it's first level and category is root of a store
        $item['allowDrag'] = $allowMove && (($node->getLevel()==1 && $rootForStores) ? false : true);

        if ((int)$node->getChildrenCount()>0) {
            $item['children'] = array();
        }

        $isParent = $this->_isParentSelectedPage($node);

        if ($node->hasChildren()) {
            $item['children'] = array();
            if (!($this->getUseAjax() && $node->getLevel() > 1 && !$isParent)) {
                foreach ($node->getChildren() as $child) {
                    $item['children'][] = $this->_getNodeJson($child, $level+1);
                }
            }
        }

        if ($isParent || $node->getLevel() < 2) {
            $item['expanded'] = true;
        }

        return $item;
    }

    /**
     * Get category name
     *
     * @param Varien_Object $node
     * @return string
     */
    public function buildNodeName($node)
    {
        $result = $this->htmlEscape($node->getName());
        return $result;
    }

    protected function _isPageMoveable($node)
    {
        $options = new Varien_Object(array(
            'is_moveable' => true,
            'category' => $node
        ));

        return $options->getIsMoveable();
    }

    protected function _isParentSelectedPage($node)
    {
        if ($node && $this->getPage()) {
            $pathIds = $this->getPage()->getPathIds();
            if (in_array($node->getId(), $pathIds)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if page loaded by outside link to category edit
     *
     * @return boolean
     */
    public function isClearEdit()
    {
        return true;//(bool) $this->getRequest()->getParam('clear');
    }

    /**
     * Check availability of adding root category
     *
     * @return boolean
     */
    public function canAddRootCategory()
    {
        $options = new Varien_Object(array('is_allow'=>true));
        return $options->getIsAllow();
    }

    /**
     * Check availability of adding sub category
     *
     * @return boolean
     */
    public function canAddSubCategory()
    {
        $options = new Varien_Object(array('is_allow'=>true));
        return $options->getIsAllow();
    }
}
