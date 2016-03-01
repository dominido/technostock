<?php

class Brander_NewPost_Block_Adminhtml_Locales_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Init class
	 */
	public function __construct()
	{
		parent::__construct();

		$this->setId('locales_edit_form');
		$this->setTitle($this->__('Locale Information'));
	}

	/**
	 * Setup form fields for inserts/updates
	 *
	 * return Mage_Adminhtml_Block_Widget_Form
	 */
	protected function _prepareForm()
	{
		$model = Mage::registry('brander_newpost/locale');

		$form = new Varien_Data_Form(array(
			'id'        => 'edit_form',
			'action'    => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
			'method'    => 'post'
		));

		$fieldset = $form->addFieldset('base_fieldset', array(
			'legend'    => $this->__('Locale Information'),
		));

		if ($model->getId()) {
			$fieldset->addField('locale_id', 'hidden', array(
				'name' => 'locale_id',
			));
		}

		$fieldset->addField('name', 'text', array(
			'name'      => 'name',
			'label'     => $this->__('Name'),
			'title'     => $this->__('Name'),
			'readonly'	=> 'readonly',
		));

		$store_view_array = array();

		foreach (Mage::app()->getStores() as $store) {
			$store_view_array[] = array('label' => $store->getName(), 'value' => $store->getId());
		}

		$fieldset->addField('magento_store_id', 'select', array(
			'name'      => 'magento_store_id',
			'label'     => $this->__('Magento Store Id'),
			'title'     => $this->__('Magento Store Id'),
			'values'    => $store_view_array,
			'required'  => true,
		));

		$fieldset->addField('is_active', 'select', array(
			'name'      => 'is_active',
			'label'     => $this->__('Is active'),
			'title'     => $this->__('Is active'),
			'required'  => true,
			'values' => array(
				0 => $this->__('No'),
				1 => $this->__('Yes'),
			)
		));

		$form->setValues($model->getData());
		$form->setUseContainer(true);
		$this->setForm($form);

		return parent::_prepareForm();
	}
} 