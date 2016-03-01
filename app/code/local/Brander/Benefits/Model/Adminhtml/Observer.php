<?php
/**
 * Brander Benefits extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        Benefits
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_Benefits_Model_Adminhtml_Observer
{
    /**
     * check if tab can be added
     *
     * @access protected
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    protected function _canAddTab($product)
    {
        if ($product->getId()) {
            return true;
        }
        if (!$product->getAttributeSetId()) {
            return false;
        }
        $request = Mage::app()->getRequest();
        if ($request->getParam('type') == 'configurable') {
            if ($request->getParam('attributes')) {
                return true;
            }
        }
        return false;
    }

    /**
     * add the benefit tab to products
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Brander_Benefits_Model_Adminhtml_Observer
     */
    public function addProductBenefitBlock($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $product = Mage::registry('product');
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs && $this->_canAddTab($product)) {
            $block->addTab(
                'benefits',
                array(
                    'label' => Mage::helper('brander_benefits')->__('Benefits'),
                    'url'   => Mage::helper('adminhtml')->getUrl(
                        'adminhtml/benefits_benefit_catalog_product/benefits',
                        array('_current' => true)
                    ),
                    'class' => 'ajax',
                )
            );
        }
        return $this;
    }

    /**
     * save benefit - product relation
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Brander_Benefits_Model_Adminhtml_Observer
     */
    public function saveProductBenefitData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('benefits', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
            $product = Mage::registry('product');
            $benefitProduct = Mage::getResourceSingleton('brander_benefits/benefit_product')
                ->saveProductRelation($product, $post);
        }
        return $this;
    }
    /**
     * add the benefit tab to categories
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Brander_Benefits_Model_Adminhtml_Observer
     */
    public function addCategoryBenefitBlock($observer)
    {
        $tabs = $observer->getEvent()->getTabs();
        $content = $tabs->getLayout()->createBlock(
            'brander_benefits/adminhtml_catalog_category_tab_benefit',
            'category.benefit.grid'
        )->toHtml();
        $serializer = $tabs->getLayout()->createBlock(
            'adminhtml/widget_grid_serializer',
            'category.benefit.grid.serializer'
        );
        $serializer->initSerializerBlock(
            'category.benefit.grid',
            'getSelectedBenefits',
            'benefits',
            'category_benefits'
        );
        $serializer->addColumnInputName('position');
        $content .= $serializer->toHtml();
        $tabs->addTab(
            'benefit',
            array(
                'label'   => Mage::helper('brander_benefits')->__('Benefits'),
                'content' => $content,
            )
        );
        return $this;
    }

    /**
     * save benefit - category relation
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Brander_Benefits_Model_Adminhtml_Observer
     */
    public function saveCategoryBenefitData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('benefits', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
            $category = Mage::registry('category');
            $benefitCategory = Mage::getResourceSingleton('brander_benefits/benefit_category')
                ->saveCategoryRelation($category, $post);
        }
        return $this;
    }
}
