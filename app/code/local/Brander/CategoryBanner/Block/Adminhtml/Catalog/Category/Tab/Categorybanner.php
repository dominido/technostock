<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Category Image tab on category edit form
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
class Brander_CategoryBanner_Block_Adminhtml_Catalog_Category_Tab_Categorybanner extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('catalog_category_categorybanner');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(array('in_categorybanners'=>1));
        }
    }

    /**
     * get current category
     *
     * @access public
     * @return Mage_Catalog_Model_Category|null
     * @author Ultimate Module Creator
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * prepare the collection
     *
     * @access protected
     * @return Brander_CategoryBanner_Block_Adminhtml_Catalog_Category_Tab_Categorybanner
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('brander_categorybanner/categorybanner_collection');
        if ($this->getCategory()->getId()) {
            $constraint = 'related.category_id='.$this->getCategory()->getId();
        } else {
            $constraint = 'related.category_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('brander_categorybanner/categorybanner_category')),
            'related.categorybanner_id=main_table.entity_id AND '.$constraint,
            array('position')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare the columns
     *
     * @access protected
     * @return Brander_CategoryBanner_Block_Adminhtml_Catalog_Category_Tab_Categorybanner
     * @author Ultimate Module Creator
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_categorybanners',
            array(
                'header_css_class'  => 'a-center',
                'type'   => 'checkbox',
                'name'   => 'in_categorybanners',
                'values' => $this->_getSelectedCategorybanners(),
                'align'  => 'center',
                'index'  => 'entity_id'
            )
        );
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('brander_categorybanner')->__('Id'),
                'type'   => 'number',
                'align'  => 'left',
                'index'  => 'entity_id',
            )
        );
        $this->addColumn(
            'category_image_name',
            array(
                'header' => Mage::helper('brander_categorybanner')->__('category image name'),
                'align'  => 'left',
                'index'  => 'category_image_name',
                'renderer' => 'brander_categorybanner/adminhtml_helper_column_renderer_relation',
                'params' => array(
                    'id' => 'getId'
                ),
                'base_link' => 'adminhtml/categorybanner_categorybanner/edit',
            )
        );
        $this->addColumn(
            'position',
            array(
                'header'         => Mage::helper('brander_categorybanner')->__('Position'),
                'name'           => 'position',
                'width'          => 60,
                'type'           => 'number',
                'validate_class' => 'validate-number',
                'index'          => 'position',
                'editable'       => true,
            )
        );
        return parent::_prepareColumns();
    }

    /**
     * Retrieve selected categorybanners
     *
     * @access protected
     * @return array
     * @author Ultimate Module Creator
     */
    protected function _getSelectedCategorybanners()
    {
        $categorybanners = $this->getCategoryCategorybanners();
        if (!is_array($categorybanners)) {
            $categorybanners = array_keys($this->getSelectedCategorybanners());
        }
        return $categorybanners;
    }

    /**
     * Retrieve selected categorybanners
     *
     * @access protected
     * @return array
     * @author Ultimate Module Creator
     */
    public function getSelectedCategorybanners()
    {
        $categorybanners = array();
        //used helper here in order not to override the category model
        $selected = Mage::helper('brander_categorybanner/category')->getSelectedCategorybanners(Mage::registry('current_category'));
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $categorybanner) {
            $categorybanners[$categorybanner->getId()] = array('position' => $categorybanner->getPosition());
        }
        return $categorybanners;
    }

    /**
     * get row url
     *
     * @access public
     * @param Brander_CategoryBanner_Model_Categorybanner
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * get grid url
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'adminhtml/categorybanner_categorybanner_catalog_category/categorybannersgrid',
            array(
                'id'=>$this->getCategory()->getId()
            )
        );
    }

    /**
     * Add filter
     *
     * @access protected
     * @param object $column
     * @return Brander_CategoryBanner_Block_Adminhtml_Catalog_Category_Tab_Categorybanner
     * @author Ultimate Module Creator
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_categorybanners') {
            $categorybannerIds = $this->_getSelectedCategorybanners();
            if (empty($categorybannerIds)) {
                $categorybannerIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$categorybannerIds));
            } else {
                if ($categorybannerIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$categorybannerIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
