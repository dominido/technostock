<?php
/**
 * Brander Benefits extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        Benefits
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */

$setup = $this;
$setup->startSetup();

$attributeName = 'redirect_url';
$attributeParams = array(

    'group'          => 'General',
    'type'           => 'varchar',
    'backend'        => '',
    'frontend'       => '',
    'label'          => 'Redirect URL',
    'input'          => 'text',
    'source'         => '',
    'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required'       => false,
    'user_defined'   => true,
    'default'        => '',
    'unique'         => false,
    'position'       => '22',
    'note'           => '',
    'visible'        => '1',
    'wysiwyg_enabled'=> '0',
);

$setup->addAttribute(
    'brander_benefits_benefit',
    $attributeName,
    $attributeParams
);

$attributeName = 'tooltip';
$attributeParams = array(

    'group'          => 'General',
    'type'           => 'varchar',
    'backend'        => '',
    'frontend'       => '',
    'label'          => 'Tooltip',
    'input'          => 'textarea',
    'source'         => '',
    'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required'       => false,
    'user_defined'   => true,
    'default'        => '',
    'unique'         => false,
    'position'       => '24',
    'note'           => '',
    'visible'        => '1',
    'wysiwyg_enabled'=> '1',
);

$setup->addAttribute(
    'brander_benefits_benefit',
    $attributeName,
    $attributeParams
);

$attributeName = 'tooltip_enable';
$attributeParams = array(
    'group'          => 'General',
    'type'           => 'int',
    'backend'        => '',
    'frontend'       => '',
    'label'          => 'Show Tooltip',
    'input'          => 'select',
    'source'         => 'eav/entity_attribute_source_boolean',
    'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required'       => '1',
    'user_defined'   => true,
    'default'        => '0',
    'unique'         => false,
    'position'       => '26',
    'note'           => '',
    'visible'        => '1',
    'wysiwyg_enabled'=> '0',
);

$setup->addAttribute(
    'brander_benefits_benefit',
    $attributeName,
    $attributeParams
);

$setup->endSetup();
