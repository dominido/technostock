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
    ->addColumn($table, 'planned_at',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            'nullable' => true,
            'default' => null,
            'comment' => 'Planned Time'
        )
    );

$installer->getConnection()
    ->addColumn($table, 'log_items_stat',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 255,
            'nullable' => true,
            'default' => null,
            'comment' => 'Item Stat'
        )
    );

$installer->getConnection()
    ->addColumn($table, 'import_filename',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 255,
            'nullable' => true,
            'default' => null,
            'comment' => 'Import Filename'
        )
    );

$installer->getConnection()
    ->addColumn($table, 'import_type',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 2,
            'nullable' => true,
            'default' => 0,
            'comment' => 'Import Type'
        )
    );

$installer->getConnection()
    ->addColumn($table, 'import_file_size',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 255,
            'nullable' => true,
            'default' => null,
            'comment' => 'Import File Size'
        )
    );

$installer->getConnection()
    ->addColumn($table, 'import_file_loadtime',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            'nullable' => true,
            'default' => null,
            'comment' => 'Import File Load Time'
        )
    );

$installer->getConnection()
    ->addColumn($table, 'updated_at',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            'nullable' => true,
            'default' => null,
            'comment' => 'Create/Udated At'
        )
    );

$installer->getConnection()
    ->addColumn($table, 'file_type',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 2,
            'nullable' => true,
            'default' => 0,
            'comment' => 'File Type'
        )
    );

$installer->endSetup();
