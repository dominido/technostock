<?php
class Brander_Cms_Block_Adminhtml_Page_Abstract extends Mage_Adminhtml_Block_Template
{
    public function getNode($parentNodePage, $recursionLevel=2)
    {
        $tree = Mage::getResourceModel('cmsadvanced/page_tree');

        $nodeId     = $parentNodePage->getId();
        $parentId   = $parentNodePage->getParentId();

        $node = $tree->loadNode($nodeId);
        $node->loadChildren($recursionLevel);

        if ($node && $nodeId != Mage_Catalog_Model_Category::TREE_ROOT_ID) {
            $node->setIsVisible(true);
        } elseif($node && $node->getId() == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
            $node->setName(Mage::helper('catalog')->__('Root'));
        }

        $tree->addCollectionData($this->getPageCollection());

        return $node;
    }

    public function getRoot($parentNodeCategory=null, $recursionLevel=3)
    {
        if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }
        $root = Mage::registry('root');
        if (is_null($root)) {
            $storeId = (int) $this->getRequest()->getParam('store');

            if ($storeId) {
                $store = Mage::app()->getStore($storeId);
                $rootId = 1;//$store->getRootCategoryId();
            }
            else {
                $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
            }

            $tree = Mage::getResourceSingleton('cmsadvanced/page_tree')
                ->load(null, $recursionLevel);
            
            /*if ($this->getPage()) {
                $tree->loadEnsuredNodes($this->getPage(), $tree->getNodeById($rootId));
            }*/

            $tree->addCollectionData($this->getPageCollection());

            $root = $tree->getNodeById($rootId);

            if ($root && $rootId != Mage_Catalog_Model_Category::TREE_ROOT_ID) {
                $root->setIsVisible(true);
            }
            elseif($root && $root->getId() == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
                $root->setName(Mage::helper('catalog')->__('Root'));
            }

            Mage::register('root', $root);
        }

        return $root;
    }


    public function getRootIds()
    {
        return array(1);
    }

    public function getPageId()
    {
        return $this->getPage()->getId();
    }

    /**
     * Retrieve page object
     */
    public function getPage()
    {
        return Mage::registry('current_page');
    }
}
