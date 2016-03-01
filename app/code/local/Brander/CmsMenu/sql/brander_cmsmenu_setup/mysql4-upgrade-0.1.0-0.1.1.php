<?php
/**
 * Brander CmsMenu extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        CmsMenu
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$entityTypeId     = $installer->getEntityTypeId('catalog_category');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute('catalog_category', 'include_in_footer_menu',  array(
    'group'    => 'General Information',
    'type'     => 'int',
    'label'    => 'Include in Footer Menu',
    'input'    => 'select',
    'source'   => 'eav/entity_attribute_source_boolean',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required' => true,
    'default'  => 0
));

$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    '4',
    'include_in_footer_menu',
    '10'
);

$attributeId = $installer->getAttributeId($entityTypeId, 'include_in_footer_menu');

$installer->run("
INSERT INTO `{$installer->getTable('catalog_category_entity_int')}`
(`entity_type_id`, `attribute_id`, `entity_id`, `value`)
    SELECT '{$entityTypeId}', '{$attributeId}', `entity_id`, '0'
        FROM `{$installer->getTable('catalog_category_entity')}`;
");

$installer->endSetup();