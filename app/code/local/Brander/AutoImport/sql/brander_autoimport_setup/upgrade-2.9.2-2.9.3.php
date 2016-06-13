<?php

/**
 * Brander AutoImpoort extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        AutoImport
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$table = $installer->getTable('autoimport/importgrid');

$installer->getConnection()
    ->addColumn($table, 'report_filenames',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 255,
            'nullable' => true,
            'default' => null,
            'comment' => 'Reports Filenames'
        )
    );

$installer->endSetup();
