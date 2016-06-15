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

$this->startSetup();
$this->run("DROP TABLE IF EXISTS {$this->getTable('autoimport/importgrid')};");

$table = $this->getConnection()
	->newTable($this->getTable('autoimport/importgrid'))
	->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'nullable'  => false,
		'primary'   => true,
		), 'Import Grid ID')

	->addColumn(
        'import_file_size', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		), 'File size')

    ->addColumn(
        'start_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Start import Time')

    ->addColumn(
        'finish_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Finish import Time')

    ->addColumn(
        'import_status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Import process status')

    ->addColumn(
        'log_filename', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
	), 'Log filename')
	->setComment('Auto Import Grid Table');

$this->getConnection()->createTable($table);

$this->endSetup();