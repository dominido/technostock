<?php
class Brander_AdminForms_Block_Adminhtml_Block_Grid_Abstract extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_blockGroup = 'adminforms';

    public function __construct()
    {
        parent::__construct();
        $this->setId('adminFormsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('block_filter_'.$this->getRequest()->getParam('block_key'));
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true,'block_key'=>$this->getRequest()->getParam('block_key')));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('entity_id' => $row->getId(),'block_key'=>$this->getRequest()->getParam('block_key')));
    }

    public function getColumnRenderers()
	{
		return array('image'=>'adminforms/adminhtml_block_grid_column_renderer_image');
	}

    protected function _afterLoadCollection()
    {
		if ($this->getCollection()->count())
			foreach ($this->getCollection()->getItems() as $item)
				foreach ($this->getColumns() as $columnId => $column)
					if ($column->getType()=='image' && $item->getData($column->getIndex()))
						$item->setData($column->getIndex(),$item->getImageUrl($item->getData($column->getIndex())));
        return $this;
    }
}
