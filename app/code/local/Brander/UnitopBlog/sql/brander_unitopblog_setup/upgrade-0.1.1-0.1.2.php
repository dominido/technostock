<?php
/**
 * Brander UnitopBlog extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        UnitopBlog
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */

$setup = $this;
$setup->startSetup();

$attributeName = 'type_video';
$entityTypeId     = $setup->getEntityTypeId('brander_unitopblog_post');

$setup->addAttribute($entityTypeId, $attributeName,  array(
    'group'          => 'General',
    'type'           => 'int',
    'backend'        => '',
    'frontend'       => '',
    'label'          => 'Video Post',
    'input'          => 'select',
    'source'         => 'eav/entity_attribute_source_boolean',
    'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required'       => '1',
    'user_defined'   => true,
    'default'        => '',
    'unique'         => false,
    'position'       => '39',
    'note'           => '',
    'visible'        => '1',
    'wysiwyg_enabled'=> '0',
));

$setup->endSetup();
