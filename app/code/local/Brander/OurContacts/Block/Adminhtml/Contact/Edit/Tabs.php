<?php
/**
 * Brander OurContacts extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        OurContacts
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_OurContacts_Block_Adminhtml_Contact_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('contact_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('brander_ourcontacts')->__('Contact Information'));
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Brander_OurContacts_Block_Adminhtml_Contact_Edit_Tabs
     */
    protected function _prepareLayout()
    {
        $contact = $this->getContact();
        $entity = Mage::getModel('eav/entity_type')
            ->load('brander_ourcontacts_contact', 'entity_type_code');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($entity->getEntityTypeId());
        $attributes->getSelect()->order('additional_table.position', 'ASC');

        $this->addTab(
            'info',
            array(
                'label'   => Mage::helper('brander_ourcontacts')->__('Contact Information'),
                'content' => $this->getLayout()->createBlock(
                    'brander_ourcontacts/adminhtml_contact_edit_tab_attributes'
                )
                ->setAttributes($attributes)
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve contact entity
     *
     * @access public
     * @return Brander_OurContacts_Model_Contact
     */
    public function getContact()
    {
        return Mage::registry('current_contact');
    }
}
