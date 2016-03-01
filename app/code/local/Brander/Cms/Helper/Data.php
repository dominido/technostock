<?php
class Brander_Cms_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getNameFromCode($code)
    {
        return implode(' ', array_map('ucfirst', explode('_', $code)));
    }

    /**
     * Returns current page
     *
     * @return Brander_Cms_Model_Page
     */
    public function getCurrentPage()
    {
        return Mage::registry('current_page');
    }

    /*
     * Returns adminforms block grid class alias (for internal use)
     */ 
    public function getBlockGrid($blockKey)
    {
        $blockGrid = 'cmsadvanced/adminhtml_block_grid';

        try {
            if ($bufBlockGrid = Mage::helper('adminforms')->getBlockGrid($blockKey)) {
                $blockGridObj = Mage::getBlockSingleton($bufBlockGrid);
                if ($blockGridObj instanceof Brander_Cms_Block_Adminhtml_Block_Grid) {
                    $blockGrid = $bufBlockGrid;
                }
            }
        } catch (Exception $e) {
            
        }

        return $blockGrid;
    }

    public function getConfig()
    {
        return Mage::getSingleton('cmsadvanced/config');
    }

    /**
     * Merge $array2 into $array1
     * 
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public function mergeArray(array $array1, array $array2)
    {  
        foreach ($array2 as $m => $n) {
            if (!isset($array1[$m])) {
                $array1[$m] = array();
            }
            
            if (is_array($n) && count($n)) {
                $array1[$m] = $this->mergeArray($array1[$m], $n);
            } else {
                $array1[$m] = $n;
            }
        }
      
        return $array1;  
    }

    public function getUrl($url)
    {
        if (preg_match('/^https?:/i', $url)) {
            return $url;
        }

        return Mage::getModel('core/url')->getDirectUrl($url);
    }

    /**
     * Checks is current page request in preview mode
     *
     * @return bool
     */
    public function isPreviewMode()
    {
        return (bool) Mage::app()->getRequest()->getParam('isPreview');
    }

    /**
     * Returns page preview url
     *
     * @param Brander_Cms_Model_Page $page
     * @return string
     */
    public function getPreviewUrl(Brander_Cms_Model_Page $page)
    {
        if ($page) {
            return Mage::getUrl('cmsadvanced/page/view', array('id' => $page->getId(), 'isPreview' => 1));
        }
    }

    /**
     * Checks if current page is home
     *
     * @return bool
     */
    public function isHomePage()
    {
        if ($currentPage = $this->getCurrentPage()) {
            return $currentPage->isHomePage();
        }

        return false;
    }

    /**
     * Returns current page's breadcrumb path
     * 
     * @return array
     */
    public function getBreadcrumbPath()
    {
        $path = array();

        if (($currentPage = $this->getCurrentPage()) && !$this->isHomePage()) {
			$parentIds = $currentPage->getParentIds();
            $parentIds = array_slice($parentIds, 2);

            $parents = $currentPage->getParentCollection($parentIds, array('name'));

            foreach ($parents as $parent) {
                $path['page' . $parent->getId()] = array(
                    'label' => $parent->getName(),
                    'link' => $parent->getUrl()
                );
            }

            $path['page' . $currentPage->getId()] = array(
                'label' => $currentPage->getName()
            );
        }

        return $path;
    }
}
