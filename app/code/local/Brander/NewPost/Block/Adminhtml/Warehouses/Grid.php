<?php

class Brander_NewPost_Block_Adminhtml_Warehouses_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setDefaultSort('warehouse_id');
		$this->setId('warehouses_grid');
		$this->setDefaultDir('asc');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('brander_newpost/warehouse')
			->getFullCollection();

		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _filterWarehouseIdCallback($collection, $column)
	{
		$filterValue = $column->getFilter()->getValue();
		$collection->getSelect()->where("a.warehouse_id = ?", $filterValue);
		return $this;
	}

	protected function _prepareColumns()
	{
		$this->addColumn('warehouse_id',
			array(
				'header' => $this->__('ID'),
				'align' => 'right',
				'width' => '50px',
				'index' => 'warehouse_id',
				'filter_condition_callback' => array($this, '_filterWarehouseIdCallback'),
			)
		);

		$this->addColumn('name',
			array(
				'header' => $this->__('Address'),
				'index' => 'name'
			)
		);

		$this->addColumn('city_id',
			array(
				'header' => $this->__('City'),
				'index' => 'city_id',
				'type'  => 'options',
				'options' => Mage::getModel('brander_newpost/city')->getAdminOptionArray()
			)
		);

		$this->addColumn('phone',
			array(
				'header' => $this->__('Phone'),
				'index' => 'phone'
			)
		);

		$this->addColumn('max_weight_allowed',
			array(
				'header' => $this->__('Max weight'),
				'index' => 'max_weight_allowed'
			)
		);

		return parent::_prepareColumns();
	}

	public function getRowUrl($row)
	{
		return false;
	}
}