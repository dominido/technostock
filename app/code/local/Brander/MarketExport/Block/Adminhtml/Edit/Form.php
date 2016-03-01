<?php
/**
 * Brander MarketExport extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        MarketExport
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_MarketExport_Block_Adminhtml_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        //$this->setTemplate('brander/marketexport/adminform.phtml');
    }

    /**
     * Prepare form fields
     **/

     protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $item = Mage::registry('export_item');
        if (is_string($item['categories'])) {
            $item['categories'] = unserialize($item['categories']);
        }
        if (is_string($item['stores'])) {
            $item['stores'] = unserialize($item['stores']);
        }

        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend' => $this->__('Export File Settings'))
        );

        $fieldset->addField('name', 'text',
            array(
                'name' => 'name',
                'label' => $this->__('Name'),
                'title' => $this->__('Name'),
                'required' => true,
            )
        );

        $path = $item ? $this->__('Current path is %s', Mage::helper('marketexport')->getExportUrl($item['path'])) : null;
        $fieldset->addField('path', 'text',
            array(
                'name' => 'path',
                'label' => $this->__('File name'),
                'title' => $this->__('File name'),
                'required' => true,
                'note' => $path
            )
        );

        $fieldset->addField('type', 'select',
            array(
                'name' => 'type',
                'label' => $this->__('Type'),
                'title' => $this->__('Type'),
                'options' => Brander_MarketExport_Model_Export::$_MARKET_EXPORT_TYPES,
                'required' => true,
                'class' => 'validate-select',
            )
        );

        $fieldset->addField('is_active', 'select',
            array(
                'name'      => 'is_active',
                'label'     => $this->__('Status'),
                'options'   => Brander_MarketExport_Model_Export::$_STATUS_ARRAY,
                'required'  => true,
                'class' => 'validate-select',
            )
        );

        $siteselector = $form->addFieldset(
            'websiteselector',
            array('legend' => $this->__('Shop Settings and Store Selector'))
        );

        $siteselector->addField('noteshopname', 'note', array(
                'text'     => $this->__('<a href="%s">Set Up values for each store</a>', '/index.php/admin/system_config/edit/section/exportconfig'),
            ));

        $siteselector->addField('shopname', 'text',
            array(
                'name' => 'shopname',
                'label' => $this->__('Shop Name'),
                'title' => $this->__('Shop Name'),
                'required' => false,
                'value' => Mage::getStoreConfig('exportconfig/general/marketexport_shop_name'),
                'note'      => $this->__('Use each store config defaults if blank'),
            )
        );


        $siteselector->addField('companyname', 'text',
            array(
                'name' => 'companyname',
                'label' => $this->__('Company Name'),
                'title' => $this->__('Company Name'),
                'required' => false,
                'value' => Mage::getStoreConfig('exportconfig/general/marketexport_company_name'),
                'note'      => $this->__('Use each store config defaults if blank'),
            )
        );


        //$categories = Mage::getResourceModel('marketexport/export')->getCategories();

        $siteselector->addField('stores', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('cms')->__('Store View'),
                'title' => Mage::helper('cms')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));



        $itemscontrolfields = $form->addFieldset(
            'itemscontrol',
            array('legend' => $this->__('Items Settings'))
        );

        $categories = Mage::getResourceModel('marketexport/export')->getCategories();
        $itemscontrolfields->addField('categories', 'multiselect',
            array(
                'name'      => 'categories',
                'label'     => $this->__('Categories'),
                'values'   => $categories,
                'note'      => $this->__('all categories if no selection')
            )
        );

        $itemscontrolfields->addField('include_out_of_stock', 'select',
            array(
                'name'      => 'include_out_of_stock',
                'label'     => $this->__('Include Out of Stock'),
                'options'   => Brander_MarketExport_Model_Export::$_STATUS_ARRAY,
                'required'  => false,
            )
        );

        $itemscontrolfields->addField('min_price', 'text',
            array(
                'name'      => 'min_price',
                'label'     => $this->__('Min price'),
                'title'     => $this->__('Min price'),
            )
        );

        $itemscontrolfields->addField('max_price', 'text',
            array(
                'name'      => 'max_price',
                'label'     => $this->__('Max price'),
                'title'     => $this->__('Max price'),
            )
        );

        $itemscontrolfields->addField('description', 'textarea',
                array(
                    'name' => 'description',
                    'label' => $this->__('Description'),
                    'title' => $this->__('Description'),
                    'required' => false,
                    'note'  => 'Use {{name}} for insert product name in description'
                )
        );


    // TODO:: make fields in 7 version

        //$form->addElement('');


        //****************************************

/*        $testset = $form->addFieldset(
            '$addcustomfields',
            array('legend' => $this->__('Add Custom Attributes Settings'))
        );

        $testset -> addType('advanced_fields', 'Brander_MarketExport_Lib_Varien_Data_Form_Element_AdvancedFields');
        $testset -> addField('htmlblock', 'advanced_fields',
            array(
                'label'         => 'My Custom Element Label',
                'name'          => 'advfieldsselector',
                'required'      => false,
            )
        );*/


        $utmcontrolfields = $form->addFieldset(
            '$utmcontrol',
            array('legend' => $this->__('UTM Settings'))
        );

        $utmcontrolfields->addField('use_utm', 'select',
            array(
                'name'      => 'use_utm',
                'label'     => $this->__('Use UTM'),
                'options'   => array(
                    0 => $this->__('No'),
                    1 => $this->__('Yes'),
                ),
                'required'  => false,
            )
        );

        $utmcontrolfields->addField('utm_medium', 'text',
            array(
                'name' => 'utm_medium',
                'label' => $this->__('UTM medium'),
                'title' => $this->__('UTM medium'),
                'required' => false,
            )
        );
        $utmcontrolfields->addField('utm_source', 'text',
            array(
                'name' => 'utm_source',
                'label' => $this->__('UTM source'),
                'title' => $this->__('UTM source'),
                'required' => false,
            )
        );
        $utmcontrolfields->addField('utm_term', 'text',
            array(
                'name' => 'utm_term',
                'label' => $this->__('UTM term'),
                'title' => $this->__('UTM term'),
                'required' => false,
            )
        );
        $utmcontrolfields->addField('utm_campaign', 'text',
            array(
                'name' => 'utm_campaign',
                'label' => $this->__('UTM campaign'),
                'title' => $this->__('UTM campaign'),
                'required' => false,
            )
        );

        $fieldset->addField('entity_id', 'hidden', array('name' => 'entity_id'));

        $form->setAction($this->getUrl('*/*/add'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        if($item!=null){
            $form->setValues($item);
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
