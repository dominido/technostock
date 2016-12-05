<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Category Image model
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
class Brander_CategoryBanner_Model_Categorybanner extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'brander_categorybanner_categorybanner';
    const CACHE_TAG = 'brander_categorybanner_categorybanner';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'brander_categorybanner_categorybanner';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'categorybanner';
    protected $_categoryInstance = null;

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('brander_categorybanner/categorybanner');
    }

    /**
     * before save category image
     *
     * @access protected
     * @return Brander_CategoryBanner_Model_Categorybanner
     * @author Ultimate Module Creator
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * save category image relation
     *
     * @access public
     * @return Brander_CategoryBanner_Model_Categorybanner
     * @author Ultimate Module Creator
     */
    protected function _afterSave()
    {
        $this->getCategoryInstance()->saveCategorybannerRelation($this);
        return parent::_afterSave();
    }

    /**
     * get category relation model
     *
     * @access public
     * @return Brander_CategoryBanner_Model_Categorybanner_Category
     * @author Ultimate Module Creator
     */
    public function getCategoryInstance()
    {
        if (!$this->_categoryInstance) {
            $this->_categoryInstance = Mage::getSingleton('brander_categorybanner/categorybanner_category');
        }
        return $this->_categoryInstance;
    }

    /**
     * get selected categories array
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getSelectedCategories()
    {
        if (!$this->hasSelectedCategories()) {
            $categories = array();
            foreach ($this->getSelectedCategoriesCollection() as $category) {
                $categories[] = $category;
            }
            $this->setSelectedCategories($categories);
        }
        return $this->getData('selected_categories');
    }

    /**
     * Retrieve collection selected categories
     *
     * @access public
     * @return Brander_CategoryBanner_Resource_Categorybanner_Category_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedCategoriesCollection()
    {
        $collection = $this->getCategoryInstance()->getCategoryCollection($this);
        return $collection;
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
    
}
