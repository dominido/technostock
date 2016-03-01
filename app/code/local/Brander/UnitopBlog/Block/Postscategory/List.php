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
class Brander_UnitopBlog_Block_Postscategory_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public

     */
    public function __construct()
    {
        parent::__construct();
        $postscategories = Mage::getResourceModel('brander_unitopblog/postscategory_collection')
                         ->setStoreId(Mage::app()->getStore()->getId())
                         ->addAttributeToSelect('*')
                         ->addAttributeToFilter('status', 1);
        ;
        $postscategories->getSelect()->order('e.position');
        $this->setPostscategories($postscategories);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Postscategory_List

     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getPostscategories()->addFieldToFilter('level', 1);
/*        if ($this->_getDisplayMode() == 0) {
            $pager = $this->getLayout()->createBlock(
                'page/html_pager',
                'brander_unitopblog.postscategories.html.pager'
            )
            ->setCollection($this->getPostscategories());
            $this->setChild('pager', $pager);
            $this->getPostscategories()->load();
        }*/
        return $this;
    }

    /**
     * get the pager html
     *
     * @access public
     * @return string

     */
/*    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }*/

    /**
     * get the display mode
     *
     * @access protected
     * @return int

     */
    protected function _getDisplayMode()
    {
        return Mage::getStoreConfigFlag('brander_unitopblog/postscategory/tree');
    }

    /**
     * draw post category
     *
     * @access public
     * @param Brander_UnitopBlog_Model_Postscategory
     * @param int $level
     * @return int

     */
    public function drawPostscategory($postscategory, $level = 0)
    {
        $html = '';
        $recursion = $this->getRecursion();
        if ($recursion !== '0' && $level >= $recursion) {
            return '';
        }
        if (!$postscategory->getStatus()) {
            return '';
        }
        $postscategory->setStoreId(Mage::app()->getStore()->getId());
        $children = $postscategory->getChildrenPostscategories()->addAttributeToSelect('*');
        $activeChildren = array();
        if ($recursion == 0 || $level < $recursion-1) {
            foreach ($children as $child) {
                if ($child->getStatus()) {
                    $activeChildren[] = $child;
                }
            }
        }
        $html .= '<li>';
        $html .= '<a href="'.Mage::helper('brander_unitopblog/postcategory')->getPostscategoriesSeoUrl($postscategory).'">'.$postscategory->getTitle().'</a>';
        if (count($activeChildren) > 0) {
            $html .= '<ul>';
            foreach ($children as $child) {
                $html .= $this->drawPostscategory($child, $level+1);
            }
            $html .= '</ul>';
        }
        $html .= '</li>';
        return $html;
    }

    /**
     * get recursion
     *
     * @access public
     * @return int

     */
    public function getRecursion()
    {
        if (!$this->hasData('recursion')) {
            $this->setData('recursion', Mage::getStoreConfig('brander_unitopblog/postscategory/recursion'));
        }
        return $this->getData('recursion');
    }

    public function getActiveCategoryId()
    {
        return Mage::helper('brander_unitopblog/postscategory')->getActiveCategoryId();
    }

    public function isActiveCategory($category)
    {
        if ($category->getEntityId() == $this->getActiveCategoryId()) {
            return true;
        }
        return false;
    }
}
