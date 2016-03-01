<?php
/**
 * Magento Commercial Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Commercial Edition License
 * that is available at: http://www.magentocommerce.com/license/commercial-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Adminhtml Catalog Category Attributes per Group Tab block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Brander_Cms_Block_Adminhtml_Page_Tab_Attributes extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Retrieve Category object
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getPage()
    {
        return Mage::registry('current_page');
    }

    /**
     * Initialize tab
     *
     */
    public function __construct() {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Catalog_Category_Tab_Attributes
     */
    protected function _prepareForm() {
        $group      = $this->getGroup();
        $attributes = $this->getAttributes();

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('group_' . $group->getId());
        $form->setDataObject($this->getPage());

        $fieldset = $form->addFieldset('fieldset_group_' . $group->getId(), array(
            'legend'    => Mage::helper('catalog')->__($group->getAttributeGroupName()),
            'class'     => 'fieldset-wide',
        ));

        if ($this->getAddHiddenFields()) {
            if (!$this->getPage()->getId()) {
                // path
                if ($this->getRequest()->getParam('parent')) {
                    $fieldset->addField('path', 'hidden', array(
                        'name'  => 'path',
                        'value' => $this->getRequest()->getParam('parent')
                    ));
                }
                else {
                    $fieldset->addField('path', 'hidden', array(
                        'name'  => 'path',
                        'value' => 1
                    ));
                }
            }
            else {
                $fieldset->addField('id', 'hidden', array(
                    'name'  => 'id',
                    'value' => $this->getPage()->getId()
                ));
                $fieldset->addField('path', 'hidden', array(
                    'name'  => 'path',
                    'value' => $this->getPage()->getPath()
                ));
            }
        }

        $this->_setFieldset($attributes, $fieldset);

        if ($element = $form->getElement('custom_use_parent_settings')) {
            $element->setData('onchange', 'onCustomUseParentChanged(this)');
        }

        if (!$this->getPage()->getId()){
            $this->getPage()->setIncludeInMenu(1);
        }

        $form->addValues($this->getPage()->getData());

        $form->setFieldNameSuffix('general');
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve Additional Element Types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $config = Mage::getConfig();
       
        $types = array(
            'textarea' => $config->getBlockClassName('adminforms/adminhtml_helper_form_wysiwyg'),
            'file' => $config->getBlockClassName('adminforms/adminhtml_helper_form_file'),
            'image' => $config->getBlockClassName('cmsadvanced/adminhtml_helper_form_image'),
            'grid' => $config->getBlockClassName('cmsadvanced/adminhtml_helper_form_grid')
        );

        //append config types
        $types = array_merge($types, $this->_getAdditionalCmsElementTypes());

        return $types;
    }

    protected function _getAdditionalCmsElementTypes()
    {
        $types = array();
        
        $config = Mage::getConfig();
        $cmsTypes = Mage::helper('cmsadvanced')->getConfig()->getElementTypes();

        foreach ($cmsTypes as $typeName => $typeData) {
            $class = isset($typeData['class']) ? $typeData['class'] : null;

            if ($class) {
                $types[$typeName] = $config->getBlockClassName($class);
            }
        }

        return $types;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $renderer = Varien_Data_Form::getFieldsetElementRenderer();

        $renderer->setTemplate('cmsadvanced/widget/form/renderer/fieldset/element.phtml');
    }
}
