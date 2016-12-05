<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Admin search model
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
class Brander_CategoryBanner_Model_Adminhtml_Search_Categorybanner extends Varien_Object
{
    /**
     * Load search results
     *
     * @access public
     * @return Brander_CategoryBanner_Model_Adminhtml_Search_Categorybanner
     * @author Ultimate Module Creator
     */
    public function load()
    {
        $arr = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('brander_categorybanner/categorybanner_collection')
            ->addFieldToFilter('category_image_name', array('like' => $this->getQuery().'%'))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        foreach ($collection->getItems() as $categorybanner) {
            $arr[] = array(
                'id'          => 'categorybanner/1/'.$categorybanner->getId(),
                'type'        => Mage::helper('brander_categorybanner')->__('Category Image'),
                'name'        => $categorybanner->getCategoryImageName(),
                'description' => $categorybanner->getCategoryImageName(),
                'url' => Mage::helper('adminhtml')->getUrl(
                    '*/categorybanner_categorybanner/edit',
                    array('id'=>$categorybanner->getId())
                ),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
