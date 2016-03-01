<?php

class Brander_LayeredNavigation_Model_Catalog_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Category
{
    /**
     * Display Types
     */
    const DT_DEFAULT    = 0;
    const DT_DROPDOWN   = 1;
    const DT_WSUBCAT    = 2;
    const DT_STATIC2LVL = 3;
    const DT_ADVANCED   = 4;

    protected $_facetedData;

    protected $includedIds;
    protected $excludedIds;

    protected static $_appliedState = FALSE;

    /** @var  Brander_LayeredNavigation_Model_Url_Builder */
    protected $urlBuilder;

    protected $_displayType = null;
    protected $_advancedCollection = null;


    public function __construct()
    {
        parent::__construct();

        /** @var Brander_LayeredNavigation_Model_Url_Builder $urlBuilder */
        $urlBuilder = Mage::getModel('brander_layerednavigation/url_builder');
        $urlBuilder->reset();
        $urlBuilder->clearPagination();
        //Unitop.  clear applying filters on category url
        $urlBuilder->clearAllFilters();
        $this->urlBuilder = $urlBuilder;
        $this->_displayType = Mage::getStoreConfig('brander_layerednavigation/general/categories_type');
    }

    public function getItemsCount()
    {
        if (self::DT_ADVANCED == $this->_displayType && 'catalogsearch' != Mage::app()->getRequest()->getModuleName()) {
            return count($this->getAdvancedCollection());
        }

        return count($this->getItems());
    }

    public function getAdvancedCollection()
    {
        if (is_null($this->_advancedCollection)) {
            $helper = Mage::helper('brander_layerednavigation');
            $category = $helper->getCurrentCategory();

            $startFrom = Mage::getStoreConfig('brander_layerednavigation/advanced_categories/start_category');
            switch ($startFrom) {
                case Brander_LayeredNavigation_Model_Source_Category_Start::START_CHILDREN:
                    break;
                case Brander_LayeredNavigation_Model_Source_Category_Start::START_CURRENT:
                    $parent = $category->getParentCategory();
                    if ($parent) {
                        $category = $parent;
                    }
                    break;
                case Brander_LayeredNavigation_Model_Source_Category_Start::START_ROOT:
                default:
                    $category = Mage::getModel('catalog/category')->load(Mage::app()->getStore()->getRootCategoryId());
            }

            $cats = $this->_getCategoryCollection()->addIdFilter($category->getChildren());
            $this->_addCounts($cats);

            foreach ($cats as $c) {
                if ($c->getProductCount()) {
                    $this->_advancedCollection = $cats;
                    return $this->_advancedCollection;
                }
            }

            $this->_advancedCollection = array();
        }

        return $this->_advancedCollection;
    }

    /**
     * Using for advanced categories only
     * @param Mage_Catalog_Model_Resource_Product_Collection $categories
     */
    protected function _addCounts($categories)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = clone Mage::getSingleton('catalog/layer')->getProductCollection();
        $select = $collection->getSelect();

        $part = $select->getPart(Varien_Db_Select::FROM);

        $replaced = 0;
        if (isset($part['cat_index'])) {
            $originalPart = $part['cat_index']['joinCondition'];
            $part['cat_index']['joinCondition'] = preg_replace('/cat_index.category_id\s*=\s*\'\d+\'/i', '1', $originalPart, -1, $replaced);
            $select->setPart(Varien_Db_Select::FROM, $part);
        }

        $collection->addCountToCategories($categories);
        if ($replaced) {
            $part['cat_index']['joinCondition'] = $originalPart;
            $select->setPart(Varien_Db_Select::FROM, $part);
        }
    }

    /**
     * Using for advanced categories only
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _getCategoryCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getResourceModel('catalog/category_collection');

        $collection
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('all_children')
            ->addAttributeToSelect('is_anchor')
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToFilter('include_in_menu', 1)
            ->setOrder('position', 'asc')
            ->joinUrlRewrite();

        return $collection;
    }

    protected function _getItemsData()
    {
        $isFallback = 'catalogsearch' == Mage::app()->getRequest()->getModuleName();
        if ($isFallback) {
            $items = $this->getItemsDataFallback();
        } else {
            if ($this->_displayType == self::DT_ADVANCED) {
                // Will process in brander_layerednavigation/advanced block
                return array(0 => 1);
            }

            $startCategory = $this->getStartCategory();
            $recursive = $this->_displayType == self::DT_WSUBCAT || $this->_displayType == self::DT_STATIC2LVL;

            $items = $this->getChildrenData($startCategory, $recursive);
            if ($this->_displayType == self::DT_WSUBCAT) {
                $headingData = $this->getSubcategoriesHeadingData($startCategory);
                $items = array_merge($headingData, $items);
            }
        }

        // Hide one value
        if (Mage::getStoreConfig('brander_layerednavigation/general/hide_one_value') && count ($items) == 1) {
            $items = array();
        }

        return $items;
    }

    protected function getItemsDataFallback()
    {
        $items = parent::_getItemsData();

        // exclude
        foreach ($items as $key => $item) {
            if ($this->isExcluded($item['value'])) {
                unset($items[$key]);
            }
        }

        return $items;
    }

    /**
     * @return Mage_Catalog_Model_Category
     */
    protected function getStartCategory()
    {
        if (self::DT_STATIC2LVL == $this->_displayType) {
            $result = Mage::getModel('catalog/category')->load($this->getLayer()->getCurrentStore()->getRootCategoryId());
        } else {
            $result = $this->getCategory();
        }

        return $result;
    }

    protected function getSubcategoriesHeadingData(Mage_Catalog_Model_Category $startCategory)
    {
        $data = array();
        $rootId  = $this->getLayer()->getCurrentStore()->getRootCategoryId();

        //Get parent category of the current category
        if ($rootId != $startCategory->getId()) {
            $parent = $startCategory->getParentCategory();
            if ($parent->getId() != $rootId && !$this->isExcluded($parent->getId())){
                $data[] = $this->_prepareItemData($parent, -1);
            }

            //Add current category
            $data[] = $this->_prepareItemData($startCategory, 0);
        }

        return $data;
    }

    protected function getChildrenData(Mage_Catalog_Model_Category $start, $recursive = false, $level = 0)
    {
        $categories = $this->prepareChildrenCollection($start->getId());
        $data = array();

        foreach ($categories as $category) {
            /** @var Mage_Catalog_Model_Category $category $id */

            $id = $category->getId();
            if ($this->isExcluded($id))
            {
                continue;
            }

            $itemData = $this->_prepareItemData($category, $level + 1);
            if (is_null($itemData)) {
                continue;
            }

            $data[] = $itemData;

            if ($recursive) {
                $childrenData = $this->getChildrenData($category, false, $level + 1);
                if ($childrenData) {
                    $this->attachChildren($data, $childrenData);
                }
            }
        }
        return $data;
    }

    protected function prepareChildrenCollection($parentId)
    {
        /** @var Mage_Catalog_Model_Resource_Category_Collection|array $categories */
        $categories = Mage::getModel('catalog/category')->getCollection();
        $categories->addAttributeToSelect('name');
        $categories->addAttributeToFilter('parent_id', $parentId);
        $categories->addAttributeToFilter('include_in_menu', 1);
        $categories->addAttributeToFilter('is_active', 1);
        $categories->setOrder('position', 'asc');

        if ($this->getCountEnabled()) {
            $this->getLayer()->getProductCollection()->addCountToCategories($categories);
        }
        return $categories;
    }

    protected function attachChildren(&$data, $childrenData)
    {
        $currentIndex = count($data) - 1;

        foreach ($childrenData as $childData) {
            if ($childData['is_selected']) {
                $data[$currentIndex]['is_child_selected'] = true;
                break;
            }
        }

        $data[$currentIndex]['has_children'] = true;
        $data = array_merge($data, $childrenData);
    }

    protected function isExcluded($id)
    {
        if (is_null($this->excludedIds)) {
            $exclude = Mage::getStoreConfig('brander_layerednavigation/general/exclude_cat');
            if ($exclude){
                $this->excludedIds = explode(',', preg_replace('/[^\d,]+/','', $exclude));
            }
            else {
                $this->excludedIds = array();
            }
        }
        if (in_array($id, $this->excludedIds)) {
            return true;
        }

        if (is_null($this->includedIds)) {
            $include = Mage::getStoreConfig('brander_layerednavigation/general/include_cat');
            if ($include){
                $this->includedIds = explode(',', preg_replace('/[^\d,]+/','', $include));
            }
            else {
                $this->includedIds = array();
            }
        }
        if ($this->includedIds && !in_array($id, $this->includedIds)) {
            return true;
        }

        return false;
    }

    protected function _initItems()
    {
        if ('catalogsearch' == Mage::app()->getRequest()->getModuleName())
            return parent::_initItems();

        $data  = $this->_getItemsData();
        $items = array();
        foreach ($data as $itemData) {
            if (!$itemData)
                continue;

            $obj = new Varien_Object();
            $obj->setData($itemData);
            $obj->setUrl($itemData['value']);

            $items[] = $obj;
        }
        $this->_items = $items;
        return $this;
    }

    protected function _prepareItemData(Mage_Catalog_Model_Category $category, $level = 1)
    {
        $row = null;
        $addCount = $this->getCountEnabled();
        $isSelected = $this->getCategory()->getId() == $category->getId();
        $isFolded   = $level > 1 && $this->getCategory()->getParentId() != $category->getParentId();

        /*
         * Display only active category and having products or being parents
         */
        if (!$addCount || $category->getProductCount()) {
            $row = array(
                'label'       => Mage::helper('core')->htmlEscape($category->getName()),
                'value'       => $this->getCategoryUrl($category),
                'count'       => $addCount ? $this->_getProductCount($category) : 0,
                'level'       => $level,
                'id'          => $category->getId(),
                'parent_id'   => $category->getParentId(),
                'is_folded'   => $isFolded,
                'is_selected' => $isSelected,
            );
        }
        return $row;
    }

    protected function getCountEnabled()
    {
        $enabled = Mage::getStoreConfig('catalog/layered_navigation/display_product_count');
        if (is_null($enabled)) {
            // Magento 1.4 has no option
            $enabled = true;
        }

        return false;
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    protected function getCategoryUrl($category)
    {
        $this->urlBuilder->category = $category;
        return $this->urlBuilder->getUrl();
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @return mixed
     */
    protected function _getProductCount($category)
    {
        /** @var Brander_LayeredNavigation_Helper_Data $helper */
        $helper = Mage::helper('brander_layerednavigation');
        if ($helper->useSolr()) {
            // not implemented yet
            return null;
        } else {
            return $category->getProductCount();
        }
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = (int) $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }
        $this->_categoryId = $filter;

        Mage::register('current_category_filter', $this->getCategory(), true);

        $this->_appliedCategory = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($filter);

        if ($this->_isValidCategory($this->_appliedCategory)) {
            $this->getLayer()->getProductCollection()
                ->addCategoryFilter($this->_appliedCategory);

            if (!self::$_appliedState){
                $this->getLayer()->getState()->addFilter(
                    $this->_createItem($this->_appliedCategory->getName(), $filter)
                );
            } 
            self::$_appliedState = TRUE;
        }

        return $this;
    }
}
