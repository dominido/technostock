<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Category Image resource model
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
class Brander_CategoryBanner_Model_Resource_Categorybanner extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * constructor
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        $this->_init('brander_categorybanner/categorybanner', 'entity_id');
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @access public
     * @param int $categorybannerId
     * @return array
     * @author Ultimate Module Creator
     */
    public function lookupStoreIds($categorybannerId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getTable('brander_categorybanner/categorybanner_store'), 'store_id')
            ->where('categorybanner_id = ?', (int)$categorybannerId);
        return $adapter->fetchCol($select);
    }

    /**
     * Perform operations after object load
     *
     * @access public
     * @param Mage_Core_Model_Abstract $object
     * @return Brander_CategoryBanner_Model_Resource_Categorybanner
     * @author Ultimate Module Creator
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }
        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Brander_CategoryBanner_Model_Categorybanner $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$object->getStoreId());
            $select->join(
                array('categorybanner_categorybanner_store' => $this->getTable('brander_categorybanner/categorybanner_store')),
                $this->getMainTable() . '.entity_id = categorybanner_categorybanner_store.categorybanner_id',
                array()
            )
            ->where('categorybanner_categorybanner_store.store_id IN (?)', $storeIds)
            ->order('categorybanner_categorybanner_store.store_id DESC')
            ->limit(1);
        }
        return $select;
    }

    /**
     * Assign category image to store views
     *
     * @access protected
     * @param Mage_Core_Model_Abstract $object
     * @return Brander_CategoryBanner_Model_Resource_Categorybanner
     * @author Ultimate Module Creator
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table  = $this->getTable('brander_categorybanner/categorybanner_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = array(
                'categorybanner_id = ?' => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );
            $this->_getWriteAdapter()->delete($table, $where);
        }
        if ($insert) {
            $data = array();
            foreach ($insert as $storeId) {
                $data[] = array(
                    'categorybanner_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }
            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }
        return parent::_afterSave($object);
    }
}
