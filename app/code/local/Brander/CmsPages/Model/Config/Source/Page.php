<?php

class Brander_CmsPages_Model_Config_Source_Page
{

    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $optionsAdv = $this->getNoRouteCMSPage();
            $optionsStatic = Mage::getResourceModel('cms/page_collection')
                ->load()->toOptionIdArray();

            $this->_options = array_merge($optionsAdv, $optionsStatic);
        }
        return $this->_options;
    }

    protected function getNoRouteCMSPage() {
        $pages = Mage::getModel('cmsadvanced/page')->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('page_type', 'page404')
            ->setStoreId(Mage::app()->getStore()->getId());
        $data = array();
        foreach ($pages as $page) {
            $data[] = array (
                'value'     => 'advancedCMS:'.$page->getEntityId(),
                'label'     => $page->getName(). ' - AdvCMS'
            );
        }
        return $data;
    }
}
