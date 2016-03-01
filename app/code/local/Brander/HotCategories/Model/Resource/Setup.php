<?php
/**
 * Brander HotCategories extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        HotCategories
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_HotCategories_Model_Resource_Setup extends Mage_Catalog_Model_Resource_Setup
{

    /**
     * get the default entities for hotcategories module - used at installation
     *
     * @access public
     * @return array()
     */
    public function getDefaultEntities()
    {
        $entities = array();
        $entities['brander_hotcategories_hotcategory'] = array(
            'entity_model'                  => 'brander_hotcategories/hotcategory',
            'attribute_model'               => 'brander_hotcategories/resource_eav_attribute',
            'table'                         => 'brander_hotcategories/hotcategory',
            'additional_attribute_table'    => 'brander_hotcategories/eav_attribute',
            'entity_attribute_collection'   => 'brander_hotcategories/hotcategory_attribute_collection',
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
                    'description' => array(
                        'group'          => 'General',
                        'type'           => 'text',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Description',
                        'input'          => 'textarea',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '0',
                        'user_defined'   => true,
                        'default'        => '',
                        'unique'         => false,
                        'position'       => '20',
                        'note'           => '',
                        'visible'        => '1',
                        'wysiwyg_enabled'=> '0',
                    ),
                    'image' => array(
                        'group'          => 'General',
                        'type'           => 'varchar',
                        'backend'        => 'brander_hotcategories/hotcategory_attribute_backend_image',
                        'frontend'       => '',
                        'label'          => 'Image',
                        'input'          => 'image',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '',
                        'user_defined'   => true,
                        'default'        => '',
                        'unique'         => false,
                        'position'       => '30',
                        'note'           => '',
                        'visible'        => '1',
                        'wysiwyg_enabled'=> '0',
                    ),
                    'mode' => array(
                        'group'          => 'General',
                        'type'           => 'int',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Category Mode',
                        'input'          => 'select',
                        'source'         => 'brander_hotcategories/adminhtml_source_urlmode',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'required'       => '0',
                        'user_defined'   => true,
                        'default'        => '',
                        'unique'         => false,
                        'position'       => '40',
                        'note'           => '',
                        'visible'        => '1',
                        'wysiwyg_enabled'=> '0',

                    ),
                    'category_id' => array(
                        'group'          => 'General',
                        'type'           => 'int',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Category Id',
                        'input'          => 'select',
                        'source'         => 'brander_hotcategories/adminhtml_source_categoriestree',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'required'       => '0',
                        'user_defined'   => true,
                        'default'        => '',
                        'unique'         => false,
                        'position'       => '50',
                        'note'           => '',
                        'visible'        => '1',
                        'wysiwyg_enabled'=> '0',
                    ),
                    'category_url' => array(
                        'group'          => 'General',
                        'type'           => 'varchar',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Site URL (without site name and language)',
                        'input'          => 'text',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '0',
                        'user_defined'   => true,
                        'default'        => '',
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
