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
class Brander_MarketExport_Lib_Varien_Data_Form_Element_AdvancedFields extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);

        //$this->setType('label');
        return $this->getElementHtml();
    }

    public function getElementHtml()
    {
        $html = Mage::app()->getLayout()->createBlock('marketexport/adminhtml_edit_formfield')->toHtml();
        return $html;
    }

    public function getLabelHTML($idSuffix = ''){
        $html = 'Make additional attributes select';
        return $html;
    }

    protected  function _addHeaderJS(){
    }

}