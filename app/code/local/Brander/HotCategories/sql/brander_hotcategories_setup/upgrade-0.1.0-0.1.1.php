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

$setup = $this;
$setup->startSetup();

$attributeName = 'grid_parts';
$attributeParams = array(
    'group'          => 'General',
    'type'           => 'int',
    'backend'        => '',
    'frontend'       => '',
    'label'          => 'Grid Parts x/12',
    'input'          => 'text',
    'source'         => '',
    'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required'       => '1',
    'user_defined'   => true,
    'default'        => '3',
    'unique'         => false,
    'position'       => '55',
    'note'           => '',
    'visible'        => '1',
    'wysiwyg_enabled'=> '0',
);

$setup->addAttribute(
    'brander_hotcategories_hotcategory',
    $attributeName,
    $attributeParams
);

$setup->endSetup();
