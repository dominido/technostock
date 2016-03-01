<?php
/**
 * Brander ProductBanners extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        ProductBanners
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_ProductBanners_Block_Adminhtml_Catalog_Product_Edit_Tab_Banner extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     * @access public

     */

    public function __construct()
    {
        parent::__construct();
        $this->setId('banner_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getProduct()->getId()) {
            $this->setDefaultFilter(array('in_banners'=>1));
        }
    }

    /**
     * prepare the banner collection
     *
     * @access protected
     * @return Brander_ProductBanners_Block_Adminhtml_Catalog_Product_Edit_Tab_Banner

     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('brander_productbanners/banner_collection')
            ->addAttributeToSelect('title')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('redirect_url')
            ->addAttributeToSelect('status')
        ;
        if ($this->getProduct()->getId()) {
            $constraint = 'related.product_id='.$this->getProduct()->getId();
        } else {
            $constraint = 'related.product_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('brander_productbanners/banner_product')),
            'related.banner_id=e.entity_id AND '.$constraint,
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
     * @return Brander_ProductBanners_Block_Adminhtml_Catalog_Product_Edit_Tab_Banner

     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * prepare the grid columns
     *
     * @access protected
     * @return Brander_ProductBanners_Block_Adminhtml_Catalog_Product_Edit_Tab_Banner

     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_banners',
            array(
                'header_css_class'  => 'a-center',
                'type'  => 'checkbox',
                'name'  => 'in_banners',
                'values'=> $this->_getSelectedBanners(),
                'align' => 'center',
                'index' => 'entity_id'
            )
        );
        $this->addColumn(
            'title',
            array(
                'header' => Mage::helper('brander_productbanners')->__('Title'),
                'align'  => 'left',
                'index'  => 'title',
                'renderer' => 'brander_productbanners/adminhtml_helper_column_renderer_relation',
                'params' => array(
                    'id' => 'getId'
                ),
                'base_link' => 'adminhtml/productbanners_banner/edit',
            )
        );

        $this->addColumn(
            'image',
            array(
                'header' => Mage::helper('brander_productbanners')->__('Banner Image'),
                'align'  => 'left',
                'index'  => 'image',
                'renderer' => 'brander_productbanners/adminhtml_helper_column_renderer_productbanner',
            )
        );

        $this->addColumn(
            'status',
            array(
                'header' => Mage::helper('brander_productbanners')->__('Status'),
                'align'  => 'left',
                'index'  => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('brander_productbanners')->__('Enabled'),
                    '0' => Mage::helper('brander_productbanners')->__('Disabled'),
                )
            )
        );

        $this->addColumn(
            'position',
            array(
                'header'         => Mage::helper('brander_productbanners')->__('Position'),
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
     * Retrieve selected banners
     *
     * @access protected
     * @return array

     */
    protected function _getSelectedBanners()
    {
        $banners = $this->getProductBanners();
        if (!is_array($banners)) {
            $banners = array_keys($this->getSelectedBanners());
        }
        return $banners;
    }

    /**
     * Retrieve selected banners
     *
     * @access protected
     * @return array

     */
    public function getSelectedBanners()
    {
        $banners = array();
        //used helper here in order not to override the product model
        $selected = Mage::helper('brander_productbanners/product')->getSelectedBanners(Mage::registry('current_product'));
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $banner) {
            $banners[$banner->getId()] = array('position' => $banner->getPosition());
        }
        return $banners;
    }

    /**
     * get row url
     *
     * @access public
     * @param Brander_ProductBanners_Model_Banner
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
            '*/*/bannersGrid',
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
     * @return Brander_ProductBanners_Block_Adminhtml_Catalog_Product_Edit_Tab_Banner

     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_banners') {
            $bannerIds = $this->_getSelectedBanners();
            if (empty($bannerIds)) {
                $bannerIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$bannerIds));
            } else {
                if ($bannerIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$bannerIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
