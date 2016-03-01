<?php
/**
 * @author
 * @copyright Copyright (c) 2015
 * @package Brander_LayeredNavigation
 */  
class Brander_LayeredNavigation_Block_Adminhtml_Filter_Edit_Tab_Values extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('valuesGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('title');
        $this->setDefaultDir('ASC'); 
    }

    protected function _prepareCollection()
    {
        $values = Mage::getResourceModel('brander_layerednavigation/value_collection')
            ->addFieldToFilter('filter_id', Mage::registry('brander_layerednavigation_filter')->getId());
        $this->setCollection($values);
        
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $hlp = Mage::helper('brander_layerednavigation');

        $this->addColumn('option_id', array(
            'header'    => $hlp->__('ID'),
            'index'     => 'option_id',
            'width'     => '50px', 
        ));
       
        $this->addColumn('title', array(
            'header'    => $hlp->__('Title'),
            'index'     => 'title',
            'getter'    => 'getCurrentTitle',
        ));

        $this->addColumn('url_alias', array(
            'header'    => $hlp->__('URL alias'),
            'index'     => 'url_alias',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/adminhtml_value/edit', array('id' => $row->getValueId()));
    }

}