<?php

$installer = $this;
$installer->startSetup();
$attrData = array(
    'group'                      => 'General',
    'input'                      => 'text',
    'type'                       => 'text',
    'label'                      => 'CommerceML ID',
    'disabled'                   => 'text',
    'backend'                    => '',
    'visible'                    => 1,
    'required'                   => 0,
    'user_defined'               => 0,
    'searchable'                 => 0,
    'filterable'                 => 0,
    'comparable'                 => 0,
    'visible_on_front'           => 0,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front'   => 0,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'sort_order'                 => 1000,
);
$installer->addAttribute('catalog_product', 'commerceml_id', $attrData);
$installer->endSetup();