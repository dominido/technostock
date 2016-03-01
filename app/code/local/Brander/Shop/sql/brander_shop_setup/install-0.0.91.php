<?php

/**
 * @var $this Mage_Core_Model_Resource_Setup
 */

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$entityTypeId     = $setup->getEntityTypeId('catalog_category');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = 4;

$setup->addAttribute('catalog_category', 'category_banner', array(
    'type'          => 'varchar',
    'input'         => 'image',
    'backend'       => 'catalog/category_attribute_backend_image',
    'group'         => 'General Information',
    'label'         => 'Category Banner',
    'visible'       => 1,
    'required'      => 0,
    'user_defined'  => 1,
    'frontend_input' =>'',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible_on_front'  => 1,
));

$setup->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'category_banner',
    '4'  //sort_order
);

$installer->endSetup();


