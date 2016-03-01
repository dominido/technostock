<?php
/**
 * Brander UnitopBlog extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        UnitopBlog
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
$this->startSetup();
$table = $this->getConnection()
    ->newTable($this->getTable('brander_unitopblog/postscategory'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Entity ID'
    )
    ->addColumn(
        'entity_type_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ),
        'Entity Type ID'
    )
    ->addColumn(
        'attribute_set_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ),
        'Attribute Set ID'
    )
    ->addColumn(
        'parent_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'unsigned'  => true,
        ),
        'Parent id'
    )
    ->addColumn(
        'path',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(),
        'Path'
    )
    ->addColumn(
        'position',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'unsigned'  => true,
        ),
        'Position'
    )
    ->addColumn(
        'level',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'unsigned'  => true,
        ),
        'Level'
    )
    ->addColumn(
        'children_count',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'unsigned'  => true,
        ),
        'Children count'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null, array(),
        'Creation Time'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Update Time'
    )
    ->addIndex(
        $this->getIdxName(
            'brander_unitopblog/postscategory',
            array('entity_type_id')
        ),
        array('entity_type_id')
    )
    ->addIndex(
        $this->getIdxName(
            'brander_unitopblog/postscategory',
            array('attribute_set_id')
        ),
        array('attribute_set_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'brander_unitopblog/postscategory',
            'attribute_set_id',
            'eav/attribute_set',
            'attribute_set_id'
        ),
        'attribute_set_id',
        $this->getTable('eav/attribute_set'),
        'attribute_set_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'brander_unitopblog/postscategory',
            'entity_type_id',
            'eav/entity_type',
            'entity_type_id'
        ),
        'entity_type_id',
        $this->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Post Category Table');
$this->getConnection()->createTable($table);

$postscategoryEav = array();
$postscategoryEav['int'] = array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'length'    => null,
    'comment'   => 'Post Category Datetime Attribute Backend Table'
);

$postscategoryEav['varchar'] = array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 255,
    'comment'   => 'Post Category Varchar Attribute Backend Table'
);

$postscategoryEav['text'] = array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => '64k',
    'comment'   => 'Post Category Text Attribute Backend Table'
);

$postscategoryEav['datetime'] = array(
    'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
    'length'    => null,
    'comment'   => 'Post Category Datetime Attribute Backend Table'
);

$postscategoryEav['decimal'] = array(
    'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'length'    => '12,4',
    'comment'   => 'Post Category Datetime Attribute Backend Table'
);

foreach ($postscategoryEav as $type => $options) {
    $table = $this->getConnection()
        ->newTable($this->getTable(array('brander_unitopblog/postscategory', $type)))
        ->addColumn(
            'value_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
            ),
            'Value ID'
        )
        ->addColumn(
            'entity_type_id',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
            ),
            'Entity Type ID'
        )
        ->addColumn(
            'attribute_id',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
            ),
            'Attribute ID'
        )
        ->addColumn(
            'store_id',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
            ),
            'Store ID'
        )
        ->addColumn(
            'entity_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
            ),
            'Entity ID'
        )
        ->addColumn(
            'value',
            $options['type'],
            $options['length'], array(),
            'Value'
        )
        ->addIndex(
            $this->getIdxName(
                array('brander_unitopblog/postscategory', $type),
                array('entity_id', 'attribute_id', 'store_id'),
                Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
            ),
            array('entity_id', 'attribute_id', 'store_id'),
            array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
        )
        ->addIndex(
            $this->getIdxName(
                array('brander_unitopblog/postscategory', $type),
                array('store_id')
            ),
            array('store_id')
        )
        ->addIndex(
            $this->getIdxName(
                array('brander_unitopblog/postscategory', $type),
                array('entity_id')
            ),
            array('entity_id')
        )
        ->addIndex(
            $this->getIdxName(
                array('brander_unitopblog/postscategory', $type),
                array('attribute_id')
            ),
            array('attribute_id')
        )
        ->addForeignKey(
            $this->getFkName(
                array('brander_unitopblog/postscategory', $type),
                'attribute_id',
                'eav/attribute',
                'attribute_id'
            ),
            'attribute_id',
            $this->getTable('eav/attribute'),
            'attribute_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $this->getFkName(
                array('brander_unitopblog/postscategory', $type),
                'entity_id',
                'brander_unitopblog/postscategory',
                'entity_id'
            ),
            'entity_id',
            $this->getTable('brander_unitopblog/postscategory'),
            'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $this->getFkName(
                array('brander_unitopblog/postscategory', $type),
                'store_id',
                'core/store',
                'store_id'
            ),
            'store_id',
            $this->getTable('core/store'),
            'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->setComment($options['comment']);
    $this->getConnection()->createTable($table);
}
$table = $this->getConnection()
    ->newTable($this->getTable('brander_unitopblog/post'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Entity ID'
    )
    ->addColumn(
        'entity_type_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ),
        'Entity Type ID'
    )
    ->addColumn(
        'attribute_set_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ),
        'Attribute Set ID'
    )

    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null, array(),
        'Creation Time'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Update Time'
    )
    ->addIndex(
        $this->getIdxName(
            'brander_unitopblog/post',
            array('entity_type_id')
        ),
        array('entity_type_id')
    )
    ->addIndex(
        $this->getIdxName(
            'brander_unitopblog/post',
            array('attribute_set_id')
        ),
        array('attribute_set_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'brander_unitopblog/post',
            'attribute_set_id',
            'eav/attribute_set',
            'attribute_set_id'
        ),
        'attribute_set_id',
        $this->getTable('eav/attribute_set'),
        'attribute_set_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'brander_unitopblog/post',
            'entity_type_id',
            'eav/entity_type',
            'entity_type_id'
        ),
        'entity_type_id',
        $this->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Post Table');
$this->getConnection()->createTable($table);

$postEav = array();
$postEav['int'] = array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'length'    => null,
    'comment'   => 'Post Datetime Attribute Backend Table'
);

$postEav['varchar'] = array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 255,
    'comment'   => 'Post Varchar Attribute Backend Table'
);

$postEav['text'] = array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => '64k',
    'comment'   => 'Post Text Attribute Backend Table'
);

$postEav['datetime'] = array(
    'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
    'length'    => null,
    'comment'   => 'Post Datetime Attribute Backend Table'
);

$postEav['decimal'] = array(
    'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'length'    => '12,4',
    'comment'   => 'Post Datetime Attribute Backend Table'
);

foreach ($postEav as $type => $options) {
    $table = $this->getConnection()
        ->newTable($this->getTable(array('brander_unitopblog/post', $type)))
        ->addColumn(
            'value_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
            ),
            'Value ID'
        )
        ->addColumn(
            'entity_type_id',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
            ),
            'Entity Type ID'
        )
        ->addColumn(
            'attribute_id',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
            ),
            'Attribute ID'
        )
        ->addColumn(
            'store_id',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
            ),
            'Store ID'
        )
        ->addColumn(
            'entity_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
            ),
            'Entity ID'
        )
        ->addColumn(
            'value',
            $options['type'],
            $options['length'], array(),
            'Value'
        )
        ->addIndex(
            $this->getIdxName(
                array('brander_unitopblog/post', $type),
                array('entity_id', 'attribute_id', 'store_id'),
                Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
            ),
            array('entity_id', 'attribute_id', 'store_id'),
            array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
        )
        ->addIndex(
            $this->getIdxName(
                array('brander_unitopblog/post', $type),
                array('store_id')
            ),
            array('store_id')
        )
        ->addIndex(
            $this->getIdxName(
                array('brander_unitopblog/post', $type),
                array('entity_id')
            ),
            array('entity_id')
        )
        ->addIndex(
            $this->getIdxName(
                array('brander_unitopblog/post', $type),
                array('attribute_id')
            ),
            array('attribute_id')
        )
        ->addForeignKey(
            $this->getFkName(
                array('brander_unitopblog/post', $type),
                'attribute_id',
                'eav/attribute',
                'attribute_id'
            ),
            'attribute_id',
            $this->getTable('eav/attribute'),
            'attribute_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $this->getFkName(
                array('brander_unitopblog/post', $type),
                'entity_id',
                'brander_unitopblog/post',
                'entity_id'
            ),
            'entity_id',
            $this->getTable('brander_unitopblog/post'),
            'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $this->getFkName(
                array('brander_unitopblog/post', $type),
                'store_id',
                'core/store',
                'store_id'
            ),
            'store_id',
            $this->getTable('core/store'),
            'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->setComment($options['comment']);
    $this->getConnection()->createTable($table);
}
$table = $this->getConnection()
    ->newTable($this->getTable('brander_unitopblog/post_product'))
    ->addColumn(
        'rel_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Relation ID'
    )
    ->addColumn(
        'post_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ),
        'Post ID'
    )
    ->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ),
        'Product ID'
    )
    ->addColumn(
        'position',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable'  => false,
            'default'   => '0',
        ),
        'Position'
    )
    ->addIndex(
        $this->getIdxName(
            'brander_unitopblog/post_product',
            array('product_id')
        ),
        array('product_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'brander_unitopblog/post_product',
            'post_id',
            'brander_unitopblog/post',
            'entity_id'
        ),
        'post_id',
        $this->getTable('brander_unitopblog/post'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'brander_unitopblog/post_product',
            'product_id',
            'catalog/product',
            'entity_id'
        ),
        'product_id',
        $this->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addIndex(
        $this->getIdxName(
            'brander_unitopblog/post_product',
            array('post_id', 'product_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('post_id', 'product_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('Post to Product Linkage Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('brander_unitopblog/post_category'))
    ->addColumn(
        'rel_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Relation ID'
    )
    ->addColumn(
        'post_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ),
        'Post ID'
    )
    ->addColumn(
        'category_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ),
        'Category ID'
    )
    ->addColumn(
        'position',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable'  => false,
            'default'   => '0',
        ),
        'Position'
    )
    ->addIndex(
        $this->getIdxName(
            'brander_unitopblog/post_category',
            array('category_id')
        ),
        array('category_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'brander_unitopblog/post_category',
            'post_id',
            'brander_unitopblog/post',
            'entity_id'
        ),
        'post_id',
        $this->getTable('brander_unitopblog/post'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'brander_unitopblog/post_category',
            'category_id',
            'catalog/category',
            'entity_id'
        ),
        'category_id',
        $this->getTable('catalog/category'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addIndex(
        $this->getIdxName(
            'brander_unitopblog/post_category',
            array('post_id', 'category_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('post_id', 'category_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('Post to Category Linkage Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('brander_unitopblog/post_comment'))
    ->addColumn(
        'comment_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Post Comment ID'
    )
    ->addColumn(
        'post_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('nullable' => false),
        'Post ID'
    )
    ->addColumn(
        'title',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('nullable' => false),
        'Comment Title'
    )
    ->addColumn(
        'comment',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        '64k',
        array('nullable' => false),
        'Comment'
    )
    ->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array('nullable' => false),
        'Comment status'
    )
    ->addColumn(
        'customer_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('nullable' => true),
        'Customer id'
    )
    ->addColumn(
        'name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('nullable' => false),
        'Customer name'
    )
    ->addColumn(
        'email',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('nullable' => false),
        'Customer email'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Post Comment Modification Time'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Post Comment Creation Time'
    )
    ->addForeignKey(
        $this->getFkName(
            'brander_unitopblog/post_comment',
            'post_id',
            'brander_unitopblog/post',
            'entity_id'
        ),
        'post_id',
        $this->getTable('brander_unitopblog/post'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'brander_unitopblog/post_comment',
            'customer_id',
            'customer/entity',
            'entity_id'
        ),
        'customer_id',
        $this->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Post Comments Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('brander_unitopblog/post_comment_store'))
    ->addColumn(
        'comment_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'nullable'  => false,
            'primary'   => true,
        ),
        'Comment ID'
    )
    ->addColumn(
        'store_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Store ID'
    )
    ->addIndex(
        $this->getIdxName(
            'brander_unitopblog/post_comment_store',
            array('store_id')
        ),
        array('store_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'brander_unitopblog/post_comment_store',
            'comment_id',
            'brander_unitopblog/post_comment',
            'comment_id'
        ),
        'comment_id',
        $this->getTable('brander_unitopblog/post_comment'),
        'comment_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'brander_unitopblog/post_comment_store',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id',
        $this->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Posts Comments To Store Linkage Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('brander_unitopblog/eav_attribute'))
    ->addColumn(
        'attribute_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Attribute ID'
    )
    ->addColumn(
        'is_global',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(),
        'Attribute scope'
    )
    ->addColumn(
        'position',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(),
        'Attribute position'
    )
    ->addColumn(
        'is_wysiwyg_enabled',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(),
        'Attribute uses WYSIWYG'
    )
    ->addColumn(
        'is_visible',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(),
        'Attribute is visible'
    )
    ->setComment('UnitopBlog attribute table');
$this->getConnection()->createTable($table);

$this->installEntities();

$this->endSetup();
