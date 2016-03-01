<?php
class Brander_AdminForms_Model_Entity_Block extends Brander_AdminForms_Model_Entity_Abstract
{
    /**
     * Initialize resource
     */
    public function __construct(array $data = array())
    {
        parent::__construct();
        if (@$data['entity_type'])
            $this->setType(isset($data['entity_type']) ? $data['entity_type'] : null);
        $this->setConnection(Mage::getSingleton('core/resource')->getConnection('core_read'), Mage::getSingleton('core/resource')->getConnection('core_write'));
    }

    public function getEntityInfo($key)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('adminforms_block'))
            ->where("`key`=?", $key);
        return $this->_getReadAdapter()->fetchRow($select);
    }
}
