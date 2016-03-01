<?php

/**
 * NOTICE OF LICENSE
 *
 * You may not give, sell, distribute, sub-license, rent, lease or lend
 * any portion of the Software or Documentation to anyone.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_SocialLogin
 * @copyright  Copyright (c) 2015 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */
class ET_SocialLogin_Block_Adminhtml_SocialReport_Filter extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareForm()
    {

        /** @var $helper ET_SocialLogin_Helper_Data */
        $helper = Mage::helper('et_sociallogin');

        $form = new Varien_Data_Form();
        $fieldSet = $form->addFieldset("search_params", array('legend' => $helper->__('Filter')));

        $groups = Mage::getModel('et_sociallogin/customerGroups')->toOptionArray();
        $fieldSet->addField('group_id', 'select', array(
            'name' => 'group_id',
            'label' => Mage::helper('reports')->__('Users Group'),
            'title' => Mage::helper('reports')->__('Users Group'),
            'values' => $groups
        ));

        $form->setUseContainer(true);
        $form->setId('filter_form');
        $form->setMethod('post');
        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function _initFormValues()
    {
        $data = $this->getFilterData()->getData();
        foreach ($data as $key => $value) {
            if (is_array($value) && isset($value[0])) {
                $data[$key] = explode(',', $value[0]);
            }
        }
        $this->getForm()->addValues($data);
        return parent::_initFormValues();
    }

}
