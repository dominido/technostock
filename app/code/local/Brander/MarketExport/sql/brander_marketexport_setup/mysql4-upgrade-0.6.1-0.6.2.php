<?php
/**
 * Brander MarketExport extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        MarketExport
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
$installer = $this;

$installer->startSetup();

$installer->addAttribute('catalog_category', 'category_title_h1', array(
    'label'    => 'Category Title H1',
    'type'     => 'varchar',
    'input'    => 'text',
    'required' => false,
    'default'  => '',
    'group'    => 'General Information',
    'sort_order' => 5,
));

$installer->endSetup();
