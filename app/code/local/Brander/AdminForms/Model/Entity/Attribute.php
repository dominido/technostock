<?php
class Brander_AdminForms_Model_Entity_Attribute extends Mage_Eav_Model_Entity_Attribute
{
    const SCOPE_STORE   = 0;
    const SCOPE_GLOBAL  = 1;
    const SCOPE_WEBSITE = 2;

/*
    const MODULE_NAME   = 'Brander_AdminForms';
    const ENTITY        = 'adminforms_eav_attribute';

    protected $_eventPrefix = 'adminforms_entity_attribute';
    protected $_eventObject = 'attribute';
*/

    /**
     * Array with labels
     *
     * @var array
     */
    static protected $_labels = null;

/*    protected function _construct()
    {
        $this->_init('adminforms/attribute');
    }*/


    /**
     * Return is attribute global
     *
     * @return integer
     */
    public function getIsGlobal()
    {
        return $this->_getData('is_global');
    }

    /**
     * Retrieve attribute is global scope flag
     *
     * @return bool
     */
    public function isScopeGlobal()
    {
        return $this->getIsGlobal() == self::SCOPE_GLOBAL;
    }

    /**
     * Retrieve attribute is website scope website
     *
     * @return bool
     */
    public function isScopeWebsite()
    {
        return $this->getIsGlobal() == self::SCOPE_WEBSITE;
    }

    /**
     * Retrieve attribute is store scope flag
     *
     * @return bool
     */
    public function isScopeStore()
    {
        return !$this->isScopeGlobal() && !$this->isScopeWebsite();
    }


    /**
     * Retrieve store id
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($dataObject = $this->getDataObject()) {
            return $dataObject->getStoreId();
        }
        return $this->getData('store_id');
    }

    /**
     * Retrieve don't translated frontend label
     *
     * @return string
     */
    public function getFrontendLabel()
    {
        return $this->_getData('frontend_label');
    }

    /**
     * Get default attribute source model
     *
     * @return string
     */
    public function _getDefaultSourceModel()
    {
        return 'eav/entity_attribute_source_table';
    }

}
