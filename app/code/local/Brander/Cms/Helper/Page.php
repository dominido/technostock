<?php
class Brander_Cms_Helper_Page extends Mage_Core_Helper_Abstract
{
    public function getVisibleCollection()
    {
        $collection = Mage::getResourceModel('cmsadvanced/page_collection')
                    ->addAttributeToSelect('name')
                    ->addExcludeRootFilter()
                    ->addActiveFilter()
                    ->addUrl()
                    ->setOrder('position', 'asc')
                    ;

        return $collection;
    }

    public function getCollectionPagesByLevel($level, $select = '*')
    {
        $currentPage = Mage::helper('cmsadvanced')->getCurrentPage();
        $_allPathIds = $currentPage->getPathIds();
        $_parentId = $_allPathIds[$level - 1];
        
        $childrenPages = Mage::getModel('cmsadvanced/page')->getCollection()
                       ->addAttributeToSelect($select)
                       ->addFieldToFilter('parent_id', $_parentId)
                       ->addRedirect()
                       ->setOrder('position', 'asc')
                       ;
        
        $childrens = array();
        foreach ($childrenPages as $childrenPage) {
            $childrens[] = array('url' => $childrenPage->getUrl(), 
            'label' => $childrenPage->getName(), 
            'class' => ($currentPage->getId() == $childrenPage->getId()) ? 'current' : '');
        }
        
        return $childrens;
    }
}
