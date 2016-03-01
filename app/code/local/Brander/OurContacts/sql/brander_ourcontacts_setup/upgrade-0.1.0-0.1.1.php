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

$setup = $this;
$setup->startSetup();

$attributeName = 'operator_icon';
$attributeParams = array(

    'group'          => 'General',
    'type'           => 'varchar',
    'backend'        => 'brander_ourcontacts/contact_attribute_backend_image',
    'frontend'       => '',
    'label'          => 'Mobile Operator Icon',
    'input'          => 'image',
    'source'         => '',
    'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required'       => '',
    'user_defined'   => true,
    'default'        => '',
    'unique'         => false,
    'position'       => '15',
    'note'           => 'recommended 40x40 PNG picture',
    'visible'        => '1',
    'wysiwyg_enabled'=> '0',
);

$setup->addAttribute(
    'brander_ourcontacts_contact',
    $attributeName,
    $attributeParams
);

$setup->endSetup();
