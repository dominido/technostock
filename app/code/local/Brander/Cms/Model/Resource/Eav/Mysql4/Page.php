<?php
class Brander_Cms_Model_Resource_Eav_Mysql4_Page extends Brander_AdminForms_Model_Entity_Abstract
{
    public function _construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('cms_page');
        $this->setConnection(
            $resource->getConnection('cmsadvanced_read'),
            $resource->getConnection('cmsadvanced_write')
        );
    }

    protected function _getDefaultAttributes()
    {
        return array('entity_type_id', 'attribute_set_id', 'created_at', 'updated_at', 'parent_id', 'position', 'level', 'url_path');
    }

    /**
     * Retrieve select object for loading base entity row
     *
     * @param   Varien_Object $object
     * @param   mixed $rowId
     * @return  Zend_Db_Select
     */
    protected function _getLoadRowSelect($object, $rowId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getEntityTable())
            ->where($this->getEntityIdField()."=?", $rowId);

        return $select;
    }
    
    /**
     * Get maximum position of child categories by specific tree path
     *
     * @param   string $path
     * @return  int
     */
    protected function _getMaxPosition($path)
    {
        $select = $this->getReadConnection()->select();
        $select->from($this->getTable('cmsadvanced/page'), 'MAX(position)');
        $select->where('path ?', new Zend_Db_Expr("regexp '{$path}/[0-9]+\$'"));

        $result = 0;
        try {
            $result = (int) $this->getReadConnection()->fetchOne($select);
        } catch (Exception $e) {

        }
        return $result;
    }

    protected function _beforeSave(Varien_Object $object)
    {
        parent::_beforeSave($object);

        $path = array();
        $level = 0;
        
        if (null !== $object->getPath()) {
            $path = explode('/', $object->getPath());
            $level = count($path);
        }
        
        $object->setLevel($level);
        if (!$object->getId()) {
            $object->setPosition($this->_getMaxPosition($object->getPath()) + 1);
            if ($level) {
                $object->setParentId($path[$level - 1]);
                $object->setPath($object->getPath() . '/');
            }

            $toUpdateChild = explode('/',$object->getPath());

            $this->_getWriteAdapter()->update(
                $this->getEntityTable(),
                array('children_count'=>new Zend_Db_Expr('`children_count`+1')),
                $this->_getWriteAdapter()->quoteInto('entity_id IN(?)', $toUpdateChild)
            );
        }

        $object->setUrlPath($object->getUrlKey());
        
        return $this;
    }

    protected function _beforeDelete(Varien_Object $object)
    {
        parent::_beforeDelete($object);

        /**
         * Update children count for all parent categories
         */
        $parentIds = $object->getParentIds();
        $childDecrease = $object->getChildrenCount() + 1; // +1 is itself
        $this->_getWriteAdapter()->update(
            $this->getEntityTable(),
            array('children_count'=>new Zend_Db_Expr('`children_count`-'.$childDecrease)),
            $this->_getWriteAdapter()->quoteInto('entity_id IN(?)', $parentIds)
        );
        $this->deleteChildren($object);
        return $this;
    }

    public function deleteChildren(Varien_Object $object)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getEntityTable(), array('entity_id'))
            ->where($this->_getWriteAdapter()->quoteInto('`path` LIKE ?', $object->getPath().'/%'));

        $childrenIds = $this->_getWriteAdapter()->fetchCol($select);

        if (!empty($childrenIds)) {
            $this->_getWriteAdapter()->delete(
                $this->getEntityTable(),
                $this->_getWriteAdapter()->quoteInto('entity_id IN (?)', $childrenIds)
            );
        }

        /**
         * Add deleted children ids to object
         * This data can be used in after delete event
         */
        $object->setDeletedChildrenIds($childrenIds);
        return $this;
    }

    protected function _afterSave(Varien_Object $object)
    {
        /**
         * Add identifier for new page
         */
        if (substr($object->getPath(), -1) == '/' || !$object->getPath()) {
            $object->setPath($object->getPath() . $object->getId());
            $this->_savePath($object);
        }

        return parent::_afterSave($object);
    }

    /**
     * Update path field
     *
     * @param   Brander_Cms_Model_Page $object
     * @return  Brander_Cms_Model_Resource_Eav_Mysql4_Page
     */
    protected function _savePath($object)
    {
        if ($object->getId()) {
            $this->_getWriteAdapter()->update(
                $this->getEntityTable(),
                array('path'=>$object->getPath()),
                $this->_getWriteAdapter()->quoteInto('entity_id=?', $object->getId())
            );
        }
        return $this;
    }

    protected function _getTree()
    {
        if (!$this->_tree) {
            $this->_tree = Mage::getResourceModel('cmsadvanced/page_tree')
                ->load();
        }
        return $this->_tree;
    }

    public function getChildren($page)
    {
        $collection = $page->getCollection()
                    ->addFieldToFilter('parent_id', $page->getId())
                    ->setOrder('position', 'ASC')
                    ;

        return $collection;
    }

    /**
     * Get chlden pages count
     *
     * @param   int $pageId
     * @return  int
     */
    public function getChildrenCount($pageId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getEntityTable(), 'children_count')
            ->where('entity_id=?', $pageId);

        $child = $this->_getReadAdapter()->fetchOne($select);

        return $child;
    }

    /**
     * Move category to another parent node
     *
     * @param   Brander_Cms_Model_Page $category
     * @param   Brander_Cms_Model_Page $newParent
     * @param   null|int $afterPageId
     * @return  Brander_Cms_Model_Resource_Eav_Mysql4_Page
     */
    public function changeParent($page, $newParent, $afterPageId=null)
    {
        $childrenCount  = $this->getChildrenCount($page->getId()) + 1;
        $table          = $this->getEntityTable();
        $adapter        = $this->_getWriteAdapter();
        $pageId     = $page->getId();
        /**
         * Decrease children count for all old category parent categories
         */
        $sql = "UPDATE {$table} SET children_count=children_count-{$childrenCount} WHERE entity_id IN(?)";
        $adapter->query($adapter->quoteInto($sql, $page->getParentIds()));
        /**
         * Increase children count for new category parents
         */
        $sql = "UPDATE {$table} SET children_count=children_count+{$childrenCount} WHERE entity_id IN(?)";
        $adapter->query($adapter->quoteInto($sql, $newParent->getPathIds()));

        $position = $this->_processPositions($page, $newParent, $afterPageId);

        $newPath = $newParent->getPath().'/'.$page->getId();
        $newLevel= $newParent->getLevel()+1;
        $newUrlPath = $newParent->getUrlPath();
        $newUrlPath .= ($newUrlPath ? '/' : '' ) . $page->getData('url_key');
        
        $levelDisposition = $newLevel - $page->getLevel();

        /**
         * Update children nodes path
         */
        $sql = "UPDATE {$table} SET
            `path`  = REPLACE(`path`, '{$page->getPath()}/', '{$newPath}/'),
            `level` = `level` + {$levelDisposition},
            `url_path` = REPLACE(`url_path`, '{$page->getUrlPath()}/', '{$newUrlPath}/') 
            WHERE ". $adapter->quoteInto('path LIKE ?', $page->getPath().'/%');
        //var_dump($sql);die;
        $adapter->query($sql);
        /**
         * Update moved category data
         */
        $data = array('path' => $newPath, 'level' => $newLevel,
            'position'=>$position, 'parent_id'=>$newParent->getId(),
            'url_path' => $newUrlPath);
        $adapter->update($table, $data, $adapter->quoteInto('entity_id=?', $page->getId()));

        // Update category object to new data
        $page->addData($data);

        return $this;
    }

    /**
     * Process positions of old parent category children and new parent category children.
     * Get position for moved category
     *
     * @param   Brander_Cms_Model_Page $category
     * @param   Brander_Cms_Model_Page $newParent
     * @param   null|int $afterPageId
     * @return  int
     */
    protected function _processPositions($page, $newParent, $afterPageId)
    {
        $table          = $this->getEntityTable();
        $adapter        = $this->_getWriteAdapter();

        $sql = "UPDATE {$table} SET `position`=`position`-1 WHERE "
            . $adapter->quoteInto('parent_id=? AND ', $page->getParentId())
            . $adapter->quoteInto('position>?', $page->getPosition());
        $adapter->query($sql);

        /**
         * Prepare position value
         */
        if ($afterPageId) {
            $sql = "SELECT `position` FROM {$table} WHERE entity_id=?";
            $position = $adapter->fetchOne($adapter->quoteInto($sql, $afterPageId));

            $sql = "UPDATE {$table} SET `position`=`position`+1 WHERE "
                . $adapter->quoteInto('parent_id=? AND ', $newParent->getId())
                . $adapter->quoteInto('position>?', $position);
            $adapter->query($sql);
        } elseif ($afterPageId !== null) {
            $position = 0;
            $sql = "UPDATE {$table} SET `position`=`position`+1 WHERE "
                . $adapter->quoteInto('parent_id=? AND ', $newParent->getId())
                . $adapter->quoteInto('position>?', $position);

            $adapter->query($sql);
        } else {
            $sql = "SELECT MIN(`position`) FROM {$table} WHERE parent_id=?";
            $position = $adapter->fetchOne($adapter->quoteInto($sql, $newParent->getId()));
        }
        $position+=1;

        return $position;
    }

    public function getParentUrlKey($object)
    {
        $path = $object->getPath();
        $path = explode('/', $path);
        array_shift($path);
        array_pop($path);

        $collection = $object->getCollection()
                    ->addAttributeToSelect('url_key')
                    ->addFieldToFilter('entity_id', array('in' => $path))
                    ;

        $urlKeys = array();

        foreach ($collection as $item) {
            if ($urlKey = $item->getData('url_key')) {
                $urlKeys[] = $urlKey;
            }
        }

        return implode('/', $urlKeys);
    }

    /**
     * Save attribute
     *
     * @param Varien_Object $object
     * @param string $attributeCode
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function saveAttribute(Varien_Object $object, $attributeCode)
    {
        $attribute = $this->getAttribute($attributeCode);
        $backend = $attribute->getBackend();
        $table = $backend->getTable();
        $entity = $attribute->getEntity();
        $entityIdField = $entity->getEntityIdField();

        $row = array(
            'entity_type_id' => $entity->getTypeId(),
            'attribute_id' => $attribute->getId(),
            'store_id' => $object->getStoreId(),
            $entityIdField=> $object->getData($entityIdField),
        );

        $newValue = $object->getData($attributeCode);
        if ($attribute->isValueEmpty($newValue)) {
            $newValue = null;
        }

        $whereArr = array();
        foreach ($row as $field => $value) {
            $whereArr[] = $this->_getReadAdapter()->quoteInto("$field=?", $value);
        }
        $where = '('.join(') AND (', $whereArr).')';

        $this->_getWriteAdapter()->beginTransaction();

        try {
            $select = $this->_getWriteAdapter()->select()
                ->from($table, 'value_id')
                ->where($where);
            
            $origValueId = $this->_getWriteAdapter()->fetchOne($select);

            if ($origValueId === false && !is_null($newValue)) {
                $this->_insertAttribute($object, $attribute, $newValue);
            } elseif ($origValueId !== false && !is_null($newValue)) {
                $this->_updateAttribute($object, $attribute, $origValueId, $newValue);
            } elseif ($origValueId !== false && is_null($newValue)) {
                $this->_getWriteAdapter()->delete($table, $where);
            }
            $this->_processAttributeValues();
            $this->_getWriteAdapter()->commit();
        } catch (Exception $e) {
            $this->_getWriteAdapter()->rollback();
            throw $e;
        }

        return $this;
    }
} 
