<?php 
/**
 * Brander_Preorder extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Brander
 * @package		Brander_Preorder
 * @copyright  	Copyright (c) 2015
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Preorder module install script
 *
 * @category	Brander
 * @package		Brander_Preorder
 * @author Ultimate Module Creator
 */
$this->startSetup();
$table = $this->getConnection()
	->newTable($this->getTable('preorder/preorder'))
	->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'nullable'  => false,
		'primary'   => true,
		), 'Pre Order ID')
	->addColumn('user_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable'  => false,
		), 'User Name')

	->addColumn('user_comment', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
		), 'User comment')

	->addColumn('user_phone', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable'  => false,
		), 'User Phone')

	->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable'  => false,
		), 'Product')

	->addColumn('product_qty', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'nullable'  => false,
		'unsigned'  => true,
		), 'Qty')

	->addColumn('manager_comment', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
		), 'Manager Comment')

	->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'default' => 0,
		), 'Status')

	->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		), 'Pre Order Creation Time')
	->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		), 'Pre Order Modification Time')
	->setComment('Pre Order Table');
$this->getConnection()->createTable($table);

$table = $this->getConnection()
	->newTable($this->getTable('preorder/preorder_store'))
	->addColumn('preorder_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'nullable'  => false,
		'primary'   => true,
		), 'Pre Order ID')
	->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
		), 'Store ID')
	->addIndex($this->getIdxName('preorder/preorder_store', array('store_id')), array('store_id'))
	->addForeignKey($this->getFkName('preorder/preorder_store', 'preorder_id', 'preorder/preorder', 'entity_id'), 'preorder_id', $this->getTable('preorder/preorder'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->addForeignKey($this->getFkName('preorder/preorder_store', 'store_id', 'core/store', 'store_id'), 'store_id', $this->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->setComment('Pre Order To Store Linkage Table');
$this->getConnection()->createTable($table);
$this->endSetup();