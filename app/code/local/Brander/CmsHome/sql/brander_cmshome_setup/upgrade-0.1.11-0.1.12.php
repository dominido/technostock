<?php
/**
 * Brander CmsHome extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        CmsHome
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */

/**
 * @var $this Mage_Core_Model_Resource_Setup
 */

$installer = $this;
$installer->startSetup();

$attrData = array(
    'group'                      => 'General',
    'type'                       => 'int',
    'input'                      => 'boolean',
    'label'                      => 'Special Offers',
    'visible'                    => true,
    'required'                   => false,
    'user_defined'               => true,
    'searchable'                 => false,
    'filterable'                 => true,
    'visible_on_front'           => true,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'used_in_product_listing'  => true,
);

$installer->updateAttribute('catalog_product', 'special_offers', $attrData);

$attrData = array(
    'group'                      => 'General',
    'type'                       => 'int',
    'input'                      => 'boolean',
    'label'                      => 'Hit of Sales',
    'visible'                    => true,
    'required'                   => false,
    'user_defined'               => true,
    'searchable'                 => false,
    'filterable'                 => true,
    'visible_on_front'           => true,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'used_in_product_listing'  => true,
);
$installer->updateAttribute('catalog_product', 'hit_of_sales', $attrData);

$installer->endSetup();