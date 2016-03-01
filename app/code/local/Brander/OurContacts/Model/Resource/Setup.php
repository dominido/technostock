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
class Brander_OurContacts_Model_Resource_Setup extends Mage_Catalog_Model_Resource_Setup
{

    /**
     * get the default entities for ourcontacts module - used at installation
     *
     * @access public
     * @return array()
     */
    public function getDefaultEntities()
    {
        $entities = array();
        $entities['brander_ourcontacts_contact'] = array(
            'entity_model'                  => 'brander_ourcontacts/contact',
            'attribute_model'               => 'brander_ourcontacts/resource_eav_attribute',
            'table'                         => 'brander_ourcontacts/contact',
            'additional_attribute_table'    => 'brander_ourcontacts/eav_attribute',
            'entity_attribute_collection'   => 'brander_ourcontacts/contact_attribute_collection',
            'attributes'                    => array(
                    'title' => array(
                        'group'          => 'General',
                        'type'           => 'varchar',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Title',
                        'input'          => 'text',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '1',
                        'user_defined'   => false,
                        'default'        => '',
                        'unique'         => false,
                        'position'       => '10',
                        'note'           => '',
                        'visible'        => '1',
                        'wysiwyg_enabled'=> '0',
                    ),
                    'phone' => array(
                        'group'          => 'General',
                        'type'           => 'varchar',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Phone',
                        'input'          => 'text',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '1',
                        'user_defined'   => true,
                        'default'        => '',
                        'unique'         => false,
                        'position'       => '20',
                        'note'           => '',
                        'visible'        => '1',
                        'wysiwyg_enabled'=> '0',
                    ),
                    'phone_details' => array(
                        'group'          => 'General',
                        'type'           => 'text',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Phone Details',
                        'input'          => 'textarea',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '0',
                        'user_defined'   => true,
                        'default'        => '',
                        'unique'         => false,
                        'position'       => '30',
                        'note'           => '',
                        'visible'        => '1',
                        'wysiwyg_enabled'=> '1',
                    ),
                    'additional_text' => array(
                        'group'          => 'General',
                        'type'           => 'text',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Additional Text',
                        'input'          => 'textarea',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '0',
                        'user_defined'   => true,
                        'default'        => '',
                        'unique'         => false,
                        'position'       => '40',
                        'note'           => '',
                        'visible'        => '1',
                        'wysiwyg_enabled'=> '1',
                    ),
                    'show_in_header' => array(
                        'group'          => 'General',
                        'type'           => 'int',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Show in Header',
                        'input'          => 'select',
                        'source'         => 'eav/entity_attribute_source_boolean',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '1',
                        'user_defined'   => true,
                        'default'        => '1',
                        'unique'         => false,
                        'position'       => '50',
                        'note'           => '',
                        'visible'        => '1',
                        'wysiwyg_enabled'=> '0',
                    ),
                    'show_in_footer' => array(
                        'group'          => 'General',
                        'type'           => 'int',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Show In Footer',
                        'input'          => 'select',
                        'source'         => 'eav/entity_attribute_source_boolean',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '0',
                        'user_defined'   => true,
                        'default'        => '1',
                        'unique'         => false,
                        'position'       => '60',
                        'note'           => '',
                        'visible'        => '1',
                        'wysiwyg_enabled'=> '0',
                    ),
                    'position' => array(
                        'group'          => 'General',
                        'type'           => 'int',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Position',
                        'input'          => 'text',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '1',
                        'user_defined'   => true,
                        'default'        => '0',
                        'unique'         => false,
                        'position'       => '70',
                        'note'           => '',
                        'visible'        => '1',
                        'wysiwyg_enabled'=> '0',
                    ),
                    'status' => array(
                        'group'          => 'General',
                        'type'           => 'int',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Enabled',
                        'input'          => 'select',
                        'source'         => 'eav/entity_attribute_source_boolean',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '',
                        'user_defined'   => false,
                        'default'        => '1',
                        'unique'         => false,
                        'position'       => '80',
                        'note'           => '',
                        'visible'        => '1',
                        'wysiwyg_enabled'=> '0',
                    ),

                )
         );
        return $entities;
    }
}
