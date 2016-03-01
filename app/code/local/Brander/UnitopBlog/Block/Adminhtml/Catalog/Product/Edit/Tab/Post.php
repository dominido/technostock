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
class Brander_UnitopBlog_Block_Adminhtml_Catalog_Product_Edit_Tab_Post extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     * @access public
     */

    public function __construct()
    {
        parent::__construct();
        $this->setId('post_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getProduct()->getId()) {
            $this->setDefaultFilter(array('in_posts'=>1));
        }
    }

    /**
     * prepare the post collection
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Catalog_Product_Edit_Tab_Post
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('brander_unitopblog/post_collection')->addAttributeToSelect('title');
        if ($this->getProduct()->getId()) {
            $constraint = 'related.product_id='.$this->getProduct()->getId();
        } else {
            $constraint = 'related.product_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('brander_unitopblog/post_product')),
            'related.post_id=e.entity_id AND '.$constraint,
            array('position')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * prepare mass action grid
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Catalog_Product_Edit_Tab_Post
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * prepare the grid columns
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Catalog_Product_Edit_Tab_Post
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_posts',
            array(
                'header_css_class'  => 'a-center',
                'type'  => 'checkbox',
                'name'  => 'in_posts',
                'values'=> $this->_getSelectedPosts(),
                'align' => 'center',
                'index' => 'entity_id'
            )
        );
        $this->addColumn(
            'title',
            array(
                'header' => Mage::helper('brander_unitopblog')->__('Title'),
                'align'  => 'left',
                'index'  => 'title',
                'renderer' => 'brander_unitopblog/adminhtml_helper_column_renderer_relation',
                'params' => array(
                    'id' => 'getId'
                ),
                'base_link' => 'adminhtml/unitopblog_post/edit',
            )
        );
        $this->addColumn(
            'position',
            array(
                'header'         => Mage::helper('brander_unitopblog')->__('Position'),
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
     * Retrieve selected posts
     *
     * @access protected
     * @return array
     */
    protected function _getSelectedPosts()
    {
        $posts = $this->getProductPosts();
        if (!is_array($posts)) {
            $posts = array_keys($this->getSelectedPosts());
        }
        return $posts;
    }

    /**
     * Retrieve selected posts
     *
     * @access protected
     * @return array
     */
    public function getSelectedPosts()
    {
        $posts = array();
        //used helper here in order not to override the product model
        $selected = Mage::helper('brander_unitopblog/product')->getSelectedPosts(Mage::registry('current_product'));
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $post) {
            $posts[$post->getId()] = array('position' => $post->getPosition());
        }
        return $posts;
    }

    /**
     * get row url
     *
     * @access public
     * @param Brander_UnitopBlog_Model_Post
     * @return string
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
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/postsGrid',
            array(
                'id'=>$this->getProduct()->getId()
            )
        );
    }

    /**
     * get the current product
     *
     * @access public
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Add filter
     *
     * @access protected
     * @param object $column
     * @return Brander_UnitopBlog_Block_Adminhtml_Catalog_Product_Edit_Tab_Post
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_posts') {
            $postIds = $this->_getSelectedPosts();
            if (empty($postIds)) {
                $postIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$postIds));
            } else {
                if ($postIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$postIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
