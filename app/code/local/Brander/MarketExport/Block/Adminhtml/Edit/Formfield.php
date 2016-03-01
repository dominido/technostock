<?php
/**
 * Brander MarketExport extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        MarketExport
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_MarketExport_Block_Adminhtml_Edit_Formfield extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_attributes;
    protected $_addMapButtonHtml;
    protected $_removeMapButtonHtml;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('brander/marketexport/adminform.phtml');
    }

    /**
     * Prepare form fields
     **/


        public function getAttributes()
        {
            if (!isset($this->_attributes['product'])) {
                $attributes = Mage::getSingleton('catalog/convert_parser_product')
                        ->getExternalAttributes();
                array_splice($attributes, 0, 0, array(''=>$this->__('Choose an attribute')));
                $this->_attributes['product'] = $attributes;
        }
        return $this->_attributes['product'];
    }


    // ****  ME6 methods
    public function getMappings()
    {
        $maps = $this->getData('gui_data/map/product/db');
        return $maps ? $maps : array();
    }

    public function getAddMapButtonHtml()
    {
        if (!$this->_addMapButtonHtml) {
            $this->_addMapButtonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
                ->setClass('add')->setLabel($this->__('Add fields and their export nicknames'))
                ->setOnClick("addFieldMapping()")->toHtml();
        }
        return $this->_addMapButtonHtml;
    }

    public function getRemoveMapButtonHtml()
    {
        if (!$this->_removeMapButtonHtml) {
            $this->_removeMapButtonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
                ->setClass('delete')->setLabel($this->__('Remove'))
                ->setOnClick("removeFieldMapping(this)")->toHtml();
        }
        return $this->_removeMapButtonHtml;
    }
}
