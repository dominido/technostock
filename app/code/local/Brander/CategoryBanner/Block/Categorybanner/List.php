<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Category Image list block
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author Ultimate Module Creator
 */
class Brander_CategoryBanner_Block_Categorybanner_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $categorybanners = Mage::getResourceModel('brander_categorybanner/categorybanner_collection')
                         ->addStoreFilter(Mage::app()->getStore())
                         ->addFieldToFilter('status', 1);
        $categorybanners->setOrder('category_image_name', 'asc');
        $this->setCategorybanners($categorybanners);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Brander_CategoryBanner_Block_Categorybanner_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'brander_categorybanner.categorybanner.html.pager'
        )
        ->setCollection($this->getCategorybanners());
        $this->setChild('pager', $pager);
        $this->getCategorybanners()->load();
        return $this;
    }

    /**
     * get the pager html
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
