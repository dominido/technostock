<?php 
/**
 * Brander_FileImport extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Brander
 * @package		Brander_FileImport
 * @copyright  	Copyright (c) 2014
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * FileImport module install script
 *
 * @category	Brander
 * @package		Brander_FileImport
 * @author Ultimate Module Creator
 */
$this->startSetup();
$table = $this->getConnection()
	->newTable($this->getTable('fileimport/filegrid'))
	->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'nullable'  => false,
		'primary'   => true,
		), 'File Grid ID')
	->addColumn('file_csv_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		), 'File CSV')

	->addColumn('file_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable'  => false,
		), 'File Name')

	->addColumn('file_size', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		), 'Size file')

	->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		), 'File Grid Creation Time')
	->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		), 'File Grid Modification Time')
	->setComment('File Grid Table');
$this->getConnection()->createTable($table);

$table = $this->getConnection()
	->newTable($this->getTable('fileimport/filegrid_store'))
	->addColumn('filegrid_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'nullable'  => false,
		'primary'   => true,
		), 'File Grid ID')
	->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
		), 'Store ID')
	->addIndex($this->getIdxName('fileimport/filegrid_store', array('store_id')), array('store_id'))
	->addForeignKey($this->getFkName('fileimport/filegrid_store', 'filegrid_id', 'fileimport/filegrid', 'entity_id'), 'filegrid_id', $this->getTable('fileimport/filegrid'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->addForeignKey($this->getFkName('fileimport/filegrid_store', 'store_id', 'core/store', 'store_id'), 'store_id', $this->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->setComment('File grid To Store Linkage Table');
$this->getConnection()->createTable($table);
$this->endSetup();