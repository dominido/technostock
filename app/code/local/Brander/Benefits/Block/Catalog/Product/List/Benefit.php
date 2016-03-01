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
class Brander_Benefits_Block_Catalog_Product_List_Benefit extends Mage_Catalog_Block_Product_Abstract
{
    protected $_collection = null;
    protected $_collectionLimit = null;
    protected $_categoriesPath = array();
    protected $_excludeCategories = array("1" => "1");
    protected $_includeCategories = array();

    protected function _construct() {
        parent::_construct();
        $this->addData(array(
            'cache_lifetime' => false,
            'cache_tags' => array(
                'PRODUCT',
                'PRODUCT_BENEFITS'
            )
        ));
    }

    public function getCacheKeyInfo() {
        $cacheId = array(
            'PRODUCT_BENEFITS',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout()
        );

        return $cacheId;
    }

    /**
     * get the list of benefits
     *
     * @access protected
     * @return Brander_Benefits_Model_Resource_Benefit_Collection
     */
    public function getCollection()
    {
        if(!$this->_collection){
            $product = Mage::registry('product');
            $config = Mage::helper('brander_shop')->getCfg('brander_benefits/benefits_config');
            $this->_collectionLimit = $config->getProductpageBenefitLimit();

            // get product benefits collection
            $collection = Mage::getResourceSingleton('brander_benefits/benefit_collection')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', 1)
                ->addProductFilter($product)
                ->setOrder('order_position', 'ASC');

            if ($collection->getSize()) {
                $this->_collection = $collection;
                $this->setCollectionLimit();
                return $this->_collection;
            }

            $_categories = $product->getCategoryCollection()->getItems();

            // get product categories benefits
            foreach ($_categories as $_category) {
                if ($this->getCategoryBenefitCollection($_category)) {
                    return $this->_collection;
                }
            }

            // get product parents categories benefits
            $result = $this->getParentCategoryBenefitCollection();
            if ($result) {
                return $this->_collection;
            }

        }
        return $this->_collection;
    }

    public function getParentCategoryBenefitCollection()
    {
        $fullCategoriesList = array();
        foreach ($this->_categoriesPath as $_categories) {
            foreach ($_categories as $_categoryLvl => $_category) {
                $fullCategoriesList[$_categoryLvl][$_category] = $_category;
            }
        }

        while (count($fullCategoriesList) > 0) {
            $categoriesIds = array_pop($fullCategoriesList);
            while (count($categoriesIds) > 0) {
                $categoryId = array_pop($categoriesIds);

                if ($this->checkIfExcludeCategory($categoryId)) {
                    //if true category may exclude and return false
                    continue;
                }

                $collection = Mage::getResourceModel('brander_benefits/benefit_collection')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('status', 1)
                    ->addCategoryFilter($categoryId);


                if ($collection->getSize()) {
                    $collection->getSelect()->order('position', 'ASC');
                    $this->_collection = $collection;
                    $this->setCollectionLimit();
                    return true;
                }
            }
        }
        return false;
    }

    public function getCategoryBenefitCollection($category)
    {
        if ($this->checkIfExcludeCategory($category->getId())) {
            //if true category may exclude and return false
            return false;
        }
        $this->_categoriesPath[] = explode("/", $category->getPath());
        $collection = Mage::getResourceModel('brander_benefits/benefit_collection')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', 1)
            ->addCategoryFilter($category);


        if ($collection->getSize()){
            $collection->getSelect()->order('position', 'ASC');
            $this->_collection = $collection;
            $this->setCollectionLimit();
            return true;
        }
        return false;
    }

    protected function checkIfExcludeCategory($categoryId)
    {
        //if exclude from search return true

        if (isset($this->_excludeCategories[$categoryId]))
        {
            return true;
        } else {
            $this->_excludeCategories[$categoryId] = $categoryId;
        }
        return false;
    }

    protected function setCollectionLimit()
    {
        if ($this->_collectionLimit && $this->_collection) {
            $this->_collection->setPageSize($this->_collectionLimit)->setCurPage(1);
        }
    }
}
