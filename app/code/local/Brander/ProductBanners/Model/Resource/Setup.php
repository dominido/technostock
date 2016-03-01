<?php
/**
 * Brander ProductBanners extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        ProductBanners
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_ProductBanners_Model_Resource_Setup extends Mage_Catalog_Model_Resource_Setup
{

    /**
     * get the default entities for productbanners module - used at installation
     *
     * @access public
     * @return array()

     */
    public function getDefaultEntities()
    {
        $entities = array();
        $entities['brander_productbanners_banner'] = array(
            'entity_model'                  => 'brander_productbanners/banner',
            'attribute_model'               => 'brander_productbanners/resource_eav_attribute',
            'table'                         => 'brander_productbanners/banner',
            'additional_attribute_table'    => 'brander_productbanners/eav_attribute',
            'entity_attribute_collection'   => 'brander_productbanners/banner_attribute_collection',
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
                    'image' => array(
                        'group'          => 'General',
                        'type'           => 'varchar',
                        'backend'        => 'brander_productbanners/banner_attribute_backend_image',
                        'frontend'       => '',
                        'label'          => 'Image',
                        'input'          => 'image',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '',
                        'user_defined'   => true,
                        'default'        => '',
                        'unique'         => false,
                        'position'       => '20',
                        'note'           => '',
                        'visible'        => '1',
                        'wysiwyg_enabled'=> '0',
                    ),
                    'redirect_url' => array(
                        'group'          => 'General',
                        'type'           => 'varchar',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Redirect URL',
                        'input'          => 'text',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '1',
                        'user_defined'   => true,
                        'default'        => '',
                        'unique'         => false,
                        'position'       => '30',
                        'note'           => '',
                        'visible'        => '1',
                        'wysiwyg_enabled'=> '0',
                    ),
                    'description' => array(
                        'group'          => 'General',
                        'type'           => 'varchar',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Description',
                        'input'          => 'text',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '0',
                        'user_defined'   => true,
                        'default'        => '',
                        'unique'         => false,
                        'position'       => '40',
                        'note'           => 'show as Alt if not empty',
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
                        'position'       => '50',
                        'note'           => '',
                        'visible'        => '1',
                        'wysiwyg_enabled'=> '0',
                    ),

                )
         );
        return $entities;
    }
}
