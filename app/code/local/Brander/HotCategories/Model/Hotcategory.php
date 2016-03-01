<?php
/**
 * Brander HotCategories extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        HotCategories
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_HotCategories_Model_Hotcategory extends Mage_Catalog_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'brander_hotcategories_hotcategory';
    const CACHE_TAG = 'brander_hotcategories_hotcategory';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'brander_hotcategories_hotcategory';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'hotcategory';

    /**
     * constructor
     *
     * @access public
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('brander_hotcategories/hotcategory');
    }

    /**
     * before save hot category
     *
     * @access protected
     * @return Brander_HotCategories_Model_Hotcategory
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * save hot category relation
     *
     * @access public
     * @return Brander_HotCategories_Model_Hotcategory
     */
    protected function _afterSave()
    {
        return parent::_afterSave();
    }

    /**
     * Retrieve default attribute set id
     *
     * @access public
     * @return int
     */
    public function getDefaultAttributeSetId()
    {
        return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }

    /**
     * get attribute text value
     *
     * @access public
     * @param $attributeCode
     * @return string
     */
    public function getAttributeText($attributeCode)
    {
        $text = $this->getResource()
            ->getAttribute($attributeCode)
            ->getSource()
            ->getOptionText($this->getData($attributeCode));
        if (is_array($text)) {
            return implode(', ', $text);
        }
        return $text;
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
    
}
