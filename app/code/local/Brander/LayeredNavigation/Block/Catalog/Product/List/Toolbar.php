<?php

class Brander_LayeredNavigation_Block_Catalog_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
    protected $_pagerAlias = 'product_list_toolbar_pager';

    public function getPagerUrl($params=array())
    {
        if ($this->skip())
            return parent::getPagerUrl($params);
            
        return Mage::helper('brander_layerednavigation/url')->getFullUrl($params);
    }

    public function replacePager()
    {
        if ($this->skip()) {
            return;
        }

        $template = $this->getChild($this->_pagerAlias)->getTemplate();

        /** @var Brander_LayeredNavigation_Block_Catalog_Pager $newPager */
        $newPager = $this->getLayout()->createBlock('brander_layerednavigation/catalog_pager', $this->_pagerAlias);
        $newPager->setTemplate($template);
        $newPager->setAvailableLimit($this->getAvailableLimit());

        $newPager->assign('_type', 'html')
            ->assign('_section', 'body');

        $this->setChild($this->_pagerAlias, $newPager);

        // Fix for limit = all and not set directly in request
        $newPager->setLimit($this->getLimit());

        $newPager->setupCollection();
        $newPager->handlePrevNextTags();
    }

    private function skip()
    {
        $r = Mage::app()->getRequest();
        if (in_array($r->getModuleName(), array('supermenu', 'supermenuadmin', 'catalogsearch','tag', 'catalogsale','catalognew')))
            return true;
            
        return false;
    }

    public function setChild($alias, $block)
    {
        if ($alias == $this->_pagerAlias && $this->getChild($this->_pagerAlias) instanceof Brander_LayeredNavigation_Block_Catalog_Pager) {
            return $this;
        }
        return parent::setChild($alias, $block);
    }
}