<?php
/**
 * Brander OurContacts extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        OurContacts
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_OurContacts_Model_Contact extends Mage_Catalog_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'brander_ourcontacts_contact';
    const CACHE_TAG = 'brander_ourcontacts_contact';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'brander_ourcontacts_contact';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'contact';

    /**
     * constructor
     *
     * @access public
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('brander_ourcontacts/contact');
    }

    /**
     * before save contact
     *
     * @access protected
     * @return Brander_OurContacts_Model_Contact
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
     * get the contact Phone Details
     *
     * @access public
     * @return string
     */
    public function getPhoneDetails()
    {
        $phone_details = $this->getData('phone_details');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($phone_details);
        return $html;
    }

    /**
     * get the contact Additional Text
     *
     * @access public
     * @return string
     */
    public function getAdditionalText()
    {
        $additional_text = $this->getData('additional_text');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($additional_text);
        return $html;
    }

    /**
     * save contact relation
     *
     * @access public
     * @return Brander_OurContacts_Model_Contact
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
