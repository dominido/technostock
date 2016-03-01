<?php

class Brander_NewPost_Block_Adminhtml_Locales_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setDefaultSort('locale_id');
		$this->setId('locales_grid');
		$this->setDefaultDir('asc');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('brander_newpost/locale')
			->getCollection();

		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('locale_id',
			array(
				'header' => $this->__('ID'),
				'align' =>'right',
				'width' => '50px',
				'index' => 'locale_id'
			)
		);

		$this->addColumn('name',
			array(
				'header' => $this->__('Locale name'),
				'index' => 'name'
			)
		);

		$this->addColumn('magento_store_id',
			array(
				'header' => $this->__('Magento Store ID'),
				'index' => 'magento_store_id',
			)
		);

		$this->addColumn('is_active',
			array(
				'header' => $this->__('Is active'),
				'index' => 'is_active',
				'type'  => 'options',
				'options' => array(
					0 => 'No',
					1 => 'Yes',
				)
			)
		);

		return parent::_prepareColumns();
	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
} 