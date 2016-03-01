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
class Brander_UnitopBlog_Model_Resource_Postscategory_Tree extends Varien_Data_Tree_Dbp
{
    const ID_FIELD        = 'entity_id';
    const PATH_FIELD      = 'path';
    const ORDER_FIELD     = 'order';
    const LEVEL_FIELD     = 'level';

    /**
     * Post Categories resource collection
     *
     * @var Brander_UnitopBlog_Model_Resource_Postscategory_Collection
     */
    protected $_collection;
    protected $_storeId;

    /**
     * Inactive post categories ids
     * @var array
     */

    protected $_inactivePostscategoryIds  = null;

    /**
     * Initialize tree
     *
     * @access public
     * @return void

     */
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct(
            $resource->getConnection('brander_unitopblog_write'),
            $resource->getTableName('brander_unitopblog/postscategory'),
            array(
                Varien_Data_Tree_Dbp::ID_FIELD    => 'entity_id',
                Varien_Data_Tree_Dbp::PATH_FIELD  => 'path',
                Varien_Data_Tree_Dbp::ORDER_FIELD => 'position',
                Varien_Data_Tree_Dbp::LEVEL_FIELD => 'level',
            )
        );
    }

    /**
     * Get post categories collection
     *
     * @access public
     * @param boolean $sorted
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Collection

     */
    public function getCollection($sorted = false)
    {
        if (is_null($this->_collection)) {
            $this->_collection = $this->_getDefaultCollection($sorted);
        }
        return $this->_collection;
    }
    /**
     * set the collection
     *
     * @access public
     * @param Brander_UnitopBlog_Model_Resource_Postscategory_Collection $collection
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Tree
     */
    public function setCollection($collection)
    {
        if (!is_null($this->_collection)) {
            destruct($this->_collection);
        }
        $this->_collection = $collection;
        return $this;
    }
    /**
     * get the default collection
     *
     * @access protected
     * @param boolean $sorted
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Collection
     */
    protected function _getDefaultCollection($sorted = false)
    {
        $collection = Mage::getModel('brander_unitopblog/postscategory')->getCollection();
        $collection->addAttributeToSelect('title');
        if ($sorted) {
            if (is_string($sorted)) {
                $collection->setOrder($sorted);
            } else {
                $collection->setOrder('title');
            }
        }
        return $collection;
    }

    /**
     * Executing parents move method and cleaning cache after it
     *
     * @access public
     * @param unknown_type $postscategory
     * @param unknown_type $newParent
     * @param unknown_type $prevNode

     */
    public function move($postscategory, $newParent, $prevNode = null)
    {
        Mage::getResourceSingleton('brander_unitopblog/postscategory')
            ->move($postscategory->getId(), $newParent->getId());
        parent::move($postscategory, $newParent, $prevNode);
        $this->_afterMove($postscategory, $newParent, $prevNode);
    }

    /**
     * Move tree after
     *
     * @access protected
     * @param unknown_type $postscategory
     * @param Varien_Data_Tree_Node $newParent
     * @param Varien_Data_Tree_Node $prevNode
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Tree
     */
    protected function _afterMove($postscategory, $newParent, $prevNode)
    {
        Mage::app()->cleanCache(array(Brander_UnitopBlog_Model_Postscategory::CACHE_TAG));
        return $this;
    }

    /**
     * Load whole post category tree, that will include specified post categories ids.
     *
     * @access public
     * @param array $ids
     * @param bool $addCollectionData
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Tree

     */
    public function loadByIds($ids, $addCollectionData = true)
    {
        $levelField = $this->_conn->quoteIdentifier('level');
        $pathField  = $this->_conn->quoteIdentifier('path');
        // load first two levels, if no ids specified
        if (empty($ids)) {
            $select = $this->_conn->select()
                ->from($this->_table, 'entity_id')
                ->where($levelField . ' <= 2');
            $ids = $this->_conn->fetchCol($select);
        }
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        foreach ($ids as $key => $id) {
            $ids[$key] = (int)$id;
        }
        // collect paths of specified IDs and prepare to collect all their parents and neighbours
        $select = $this->_conn->select()
            ->from($this->_table, array('path', 'level'))
            ->where('entity_id IN (?)', $ids);
        $where = array($levelField . '=0' => true);

        foreach ($this->_conn->fetchAll($select) as $item) {
            $pathIds  = explode('/', $item['path']);
            $level = (int)$item['level'];
            while ($level > 0) {
                $pathIds[count($pathIds) - 1] = '%';
                $path = implode('/', $pathIds);
                $where["$levelField=$level AND $pathField LIKE '$path'"] = true;
                array_pop($pathIds);
                $level--;
            }
        }
        $where = array_keys($where);

        // get all required records
        if ($addCollectionData) {
            $select = $this->_createCollectionDataSelect();
        } else {
            $select = clone $this->_select;
            $select->order($this->_orderField . ' ' . Varien_Db_Select::SQL_ASC);
        }
        $select->where(implode(' OR ', $where));

        // get array of records and add them as nodes to the tree
        $arrNodes = $this->_conn->fetchAll($select);
        if (!$arrNodes) {
            return false;
        }
        $childrenItems = array();
        foreach ($arrNodes as $key => $nodeInfo) {
            $pathToParent = explode('/', $nodeInfo[$this->_pathField]);
            array_pop($pathToParent);
            $pathToParent = implode('/', $pathToParent);
            $childrenItems[$pathToParent][] = $nodeInfo;
        }
        $this->addChildNodes($childrenItems, '', null);
        return $this;
    }

    /**
     * Load array of post category parents
     *
     * @access public
     * @param string $path
     * @param bool $addCollectionData
     * @param bool $withRootNode
     * @return array

     */
    public function loadBreadcrumbsArray($path, $addCollectionData = true, $withRootNode = false)
    {
        $pathIds = explode('/', $path);
        if (!$withRootNode) {
            array_shift($pathIds);
        }
        $result = array();
        if (!empty($pathIds)) {
            if ($addCollectionData) {
                $select = $this->_createCollectionDataSelect(false);
            } else {
                $select = clone $this->_select;
            }
            $select
                ->where('e.entity_id IN(?)', $pathIds)
                ->order($this->_conn->getLengthSql('e.path') . ' ' . Varien_Db_Select::SQL_ASC);
            $result = $this->_conn->fetchAll($select);
        }
        return $result;
    }

    /**
     * Obtain select for post categories with attributes.
     * By default everything from entity table is selected
     * + name, status
     *
     * @param bool $sorted
     * @param array $optionalAttributes
     * @return Zend_Db_Select

     */
    protected function _createCollectionDataSelect($sorted = true, $optionalAttributes = array())
    {
        $select = $this->_getDefaultCollection($sorted ? $this->_orderField : false)
            ->getSelect();
        // add attributes to select
        $attributes = array('title', 'status');
        if ($optionalAttributes) {
            $attributes = array_unique(array_merge($attributes, $optionalAttributes));
        }
        foreach ($attributes as $attributeCode) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            $attribute = Mage::getResourceSingleton('brander_unitopblog/postscategory')->getAttribute($attributeCode);
            // join non-static attribute table
            if (!$attribute->getBackend()->isStatic()) {
                $tableDefault   = sprintf('d_%s', $attributeCode);
                $tableStore     = sprintf('s_%s', $attributeCode);
                $valueExpr      = $this->_conn
                    ->getCheckSql("{$tableStore}.value_id > 0", "{$tableStore}.value", "{$tableDefault}.value");

                $select
                    ->joinLeft(
                        array($tableDefault => $attribute->getBackend()->getTable()),
                        sprintf(
                            '%1$s.entity_id=e.entity_id AND %1$s.attribute_id=%2$d'.
                            ' AND %1$s.entity_type_id=e.entity_type_id AND %1$s.store_id=%3$d',
                            $tableDefault,
                            $attribute->getId(),
                            Mage_Core_Model_App::ADMIN_STORE_ID
                        ),
                        array($attributeCode => 'value')
                    )
                    ->joinLeft(
                        array($tableStore => $attribute->getBackend()->getTable()),
                        sprintf(
                            '%1$s.entity_id=e.entity_id AND %1$s.attribute_id=%2$d'.
                            ' AND %1$s.entity_type_id=e.entity_type_id AND %1$s.store_id=%3$d',
                            $tableStore,
                            $attribute->getId(),
                            $this->getStoreId()
                        ),
                        array($attributeCode => $valueExpr)
                    );
            }
        }
        return $select;
    }

    /**
     * Set store Id
     *
     * @access public
     * @param integer $storeId
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Tree

     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Return store id
     *
     * @access public
     * @return integer

     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            return Mage::app()->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * Get real existing post category ids by specified ids
     *
     * @access public
     * @param array $ids
     * @return array

     */
    public function getExistingPostscategoryIdsBySpecifiedIds($ids)
    {
        if (empty($ids)) {
            return array();
        }
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        $select = $this->_conn->select()
            ->from($this->_table, array('entity_id'))
            ->where('entity_id IN (?)', $ids);
        return $this->_conn->fetchCol($select);
    }

    /**
     * add collection data
     *
     * @access public
     * @param Brander_UnitopBlog_Model_Resource_Postscategory_Collection $collection
     * @param boolean $sorted
     * @param array $exclude
     * @param boolean $toLoad
     * @param boolean $onlyActive
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Tree

     */
    public function addCollectionData(
        $collection = null,
        $sorted = false,
        $exclude = array(),
        $toLoad = true,
        $onlyActive = false
    )
    {
        if (is_null($collection)) {
            $collection = $this->getCollection($sorted);
        } else {
            $this->setCollection($collection);
        }
        if (!is_array($exclude)) {
            $exclude = array($exclude);
        }
        $nodeIds = array();
        foreach ($this->getNodes() as $node) {
            if (!in_array($node->getId(), $exclude)) {
                $nodeIds[] = $node->getId();
            }
        }
        $collection->addIdFilter($nodeIds);
        if ($onlyActive) {
            $disabledIds = $this->_getDisabledIds($collection);
            if ($disabledIds) {
                $collection->addAttributeToFilter('entity_id', array('nin' => $disabledIds));
            }
            $collection->addAttributeToFilter('status', 1);
        }
        if ($toLoad) {
            $collection->load();
            foreach ($collection as $postscategory) {
                if ($this->getNodeById($postscategory->getId())) {
                    $this->getNodeById($postscategory->getId())->addData($postscategory->getData());
                }
            }
            foreach ($this->getNodes() as $node) {
                if (!$collection->getItemById($node->getId()) && $node->getParent()) {
                    $this->removeNode($node);
                }
            }
        }
        return $this;
    }

    /**
     * Add inactive post categories ids
     *
     * @access public
     * @param unknown_type $ids
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Tree

     */
    public function addInactivePostscategoryIds($ids)
    {
        if (!is_array($this->_inactivePostscategoryIds)) {
            $this->_initInactivePostscategoryIds();
        }
        $this->_inactivePostscategoryIds = array_merge($ids, $this->_inactivePostscategoryIds);
        return $this;
    }

    /**
     * Retrieve inactive post categories ids
     *
     * @access protected
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Tree

     */
    protected function _initInactivePostscategoryIds()
    {
        $this->_inactivePostscategoryIds = array();
        return $this;
    }
    /**
     * Retrieve inactive post categories ids
     *
     * @access public
     * @return array

     */
    public function getInactivePostscategoryIds()
    {
        if (!is_array($this->_inactivePostscategoryIds)) {
            $this->_initInactivePostscategoryIds();
        }
        return $this->_inactivePostscategoryIds;
    }

    /**
     * Return disable post category ids
     *
     * @access protected
     * @param Brander_UnitopBlog_Model_Resource_Postscategory_Collection $collection
     * @return array

     */
    protected function _getDisabledIds($collection)
    {
        $this->_inactiveItems = $this->getInactivePostscategoryIds();
        $this->_inactiveItems = array_merge(
            $this->_getInactiveItemIds($collection),
            $this->_inactiveItems
        );
        $allIds = $collection->getAllIds();
        $disabledIds = array();

        foreach ($allIds as $id) {
            $parents = $this->getNodeById($id)->getPath();
            foreach ($parents as $parent) {
                if (!$this->_getItemIsActive($parent->getId())) {
                    $disabledIds[] = $id;
                    continue;
                }
            }
        }
        return $disabledIds;
    }

    /**
     * Retrieve inactive post category item ids
     *
     * @access protecte
     * @param Brander_UnitopBlog_Model_Resource_Postscategory_Collection $collection
     * @return array

     */
    protected function _getInactiveItemIds($collection)
    {
        $filter = $collection->getAllIdsSql();
        $table = Mage::getSingleton('core/resource')->getTable('brander_unitopblog/postscategory');
        $bind = array(
            'cond' => 0,
        );
        $select = $this->_conn->select()
            ->from(array('d'=>$table), array('d.entity_id'))
            ->where('d.entity_id IN (?)', new Zend_Db_Expr($filter))
            ->where('status = :cond');
        return $this->_conn->fetchCol($select, $bind);
    }

    /**
     * Check is post category items active
     *
     * @access protecte
     * @param int $id
     * @return boolean

     */
    protected function _getItemIsActive($id)
    {
        if (!in_array($id, $this->_inactiveItems)) {
            return true;
        }
        return false;
    }
}