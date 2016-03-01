<?php
class Brander_Cms_Block_Page extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();

        if ($this->_getCachePage()->getPageType()->canCached()) {
            $this->addData(array(
                'cache_lifetime'    => false,
                'cache_tags'        => array('CMSADVANCED_PAGE')
            ));
        }
    }
    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheId = array(
            'CMSADVANCED_PAGE',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout()
        );

        $cacheId = array_merge($cacheId, $this->_getAdditionalCacheKeyInfo());

        return $cacheId;
    }

    protected function _getAdditionalCacheKeyInfo()
    {
        $page = $this->_getCachePage();
        
        $info = array(
            $page->getId(),
            $page->getUpdatedAt()
        );

        return $info;
    }

    /**
     * Returns page for caching
     *
     * @return Brander_Cms_Model_Page
     */
    protected function _getCachePage()
    {
        return $this->getPage();
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $breadcrumbsTitle = '';

        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $helper = Mage::helper('cmsadvanced');

            if (!$this->getIsHideHome() && !$helper->isHomePage()) {
                $breadcrumbsBlock->addCrumb('home', array(
                    'label' => Mage::helper('catalog')->__('Home'),
                    'title' => Mage::helper('catalog')->__('Go to Home Page'),
                    'link' => Mage::getBaseUrl()
                ));
            }

            $title = array();
            $titleSeparator = ' ' . (string) Mage::getStoreConfig('catalog/seo/title_separator') . ' ';
        
            $path  = $helper->getBreadcrumbPath();

            foreach ($path as $name => $breadcrumb) {
                $breadcrumbsBlock->addCrumb($name, $breadcrumb);
                $title[] = $breadcrumb['label'];
            }

            $breadcrumbsTitle = join($titleSeparator, array_reverse($title));
        }                

        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $page = $this->getPage();

            if (!$title = $page->getMetaTitle()) {
                $title = $breadcrumbsTitle ? $breadcrumbsTitle : $page->getName();
            }
            $headBlock->setTitle($title);
            
            if ($description = $page->getMetaDescription()) {
                $headBlock->setDescription($description);
            }
            if ($keywords = $page->getMetaKeywords()) {
                $headBlock->setKeywords($keywords);
            }

            //canonical url
            $headBlock->addLinkRel('canonical', $page->getUrl());
        }

        return $this;
    }

    /*
     * Returns current page
     *
     * @return Brander_Cms_Model_Page
     */
    public function getPage()
    {
        if (!$this->hasData('page')) {
            $page = Mage::registry('current_page');
            $this->setData('page', $page);
        }

        return $this->getData('page');
    }

    /*
     * Returns current page id
     *
     * @return int
     */
    public function getPageId()
    {
        return $this->getPage()->getId();
    }

    /*
     * Returns page attribute
     *
     * @param string $attributeCode
     * @return mixed - string or Brander_AdminForms_Model_Entity_Block_Collection
     */
    public function getAttribute($attributeCode)
    {
        $attribute = $this->getPage()->getResource()->getAttribute($attributeCode);
        
        if (!$attribute) {
            return;
        }

        switch ($attribute->getFrontendInput()) {
            case 'grid':
                $data = $this->getCollection($attributeCode);
                break;

            default:
                $data = $this->getData($attributeCode);
                break;
        }

        return $data;
    }

    /*
     * Returns collection of adminforms blocks
     *
     * @param string $attributeCode
     * @param array|string $select - field to select
     * @param bool $isStatus - use status field in filter
     * @return Brander_AdminForms_Model_Entity_Block_Collection
     */
    public function getCollection($attributeCode, $select = '*', $isStatus = true)
    {
        return $this->getPage()->getBlockCollection($attributeCode, $select, $isStatus);
    }

    /*
     * Returns content heading
     *
     * @return string - value of name attribute or content_heading attribute if it's filled
     */
    public function getContentHeading()
    {
        if (!$heading = $this->getData('content_heading')) {
            $heading = $this->getData('name');
        }

        return $heading;
    }

    /*
     * Returns page subpages collection
     *
     * @param string|array $select
     * @param bool $onlyActive
     */
    public function getChildren($select = '*', $onlyActive = true)
    {
        return $this->getPage()->getChildren($select, $onlyActive);
    }

    /*
     * Returns page parent
     *
     * @param string|array $select
     * @return Brander_Cms_Model_Page
     */
    public function getParent($select = '*')
    {
        return $this->getPage()->getParent($select);
    }

    /*
     * Returns image for attribute
     *
     * @param string $attributeCode
     * @return Brander_AdminForms_Helper_Image
     */
    public function getImage($attributeCode = 'image')
    {
        return $this->getPage()->getImage($attributeCode);
    }

    /*
     * Returns image attribute url
     *
     * @param string $attributeCode
     * @return string
     */
    public function getImageUrl($attributeCode)
    {
        return $this->getPage()->getImageUrl($attributeCode);
    }

    /*
     * Returns image size html code 
     *
     * @param string $attributeCode
     * @return string - width="imageWidth" height="imageHeight"
     */
    public function getImageSizeHtml($attributeCode)
    {
        $size = $this->getPage()->getImageSize($attributeCode);
        $sizeBuf = array();
        if (isset($size['width'])) {
            $sizeBuf[] = 'width="' . $size['width'] .'"';
        }

        if (isset($size['height'])) {
            $sizeBuf[] = 'height="' . $size['height'] . '"';
        }
        
        return implode(' ', $sizeBuf);
    }

    /*
     * Prepare attribute value with template processor
     *
     * @param string $attributeCode
     * @return string
     */
    public function getPreparedContent($attributeCode = 'content')
    {
        return $this->getPage()->getPreparedContent($attributeCode);
    }
}
