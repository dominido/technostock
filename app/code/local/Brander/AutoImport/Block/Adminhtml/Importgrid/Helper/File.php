<?php

/**
 * Brander AutoImport extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        AutoImport
 * @copyright      Copyright (c) 2014-2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */

class Brander_AutoImport_Block_Adminhtml_Importgrid_Helper_File extends Varien_Data_Form_Element_Abstract
{

	public function __construct($data){
		parent::__construct($data);
		$this->setType('file');
	}

    public function getElementHtml(){
		$html = '';
		$this->addClass('input-file');
		$html.= parent::getElementHtml();
		if ($this->getValue()) {
			$url = $this->_getUrl();
			if( !preg_match("/^http\:\/\/|https\:\/\//", $url) ) {
				$url = Mage::helper('autoimport')->getFileBaseUrl() . $url;
			}
			$html .= '<br /><a href="'.$url.'">'.$this->_getUrl().'</a> ';
		}
		$html.= $this->_getDeleteCheckbox();
		return $html;
	}

	protected function _getDeleteCheckbox(){
		$html = '';
		if ($this->getValue()) {
			$label = Mage::helper('autoimport')->__('Delete File');
			/*$html .= '<span class="delete-image">';
			$html .= '<input type="checkbox" name="'.parent::getName().'[delete]" value="1" class="checkbox" id="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? ' disabled="disabled"': '').'/>';
			$html .= '<label for="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? ' class="disabled"' : '').'> '.$label.'</label>';
			$html .= $this->_getHiddenInput();
			$html .= '</span>';*/
		}		
		return $html;
	}

	protected function _getHiddenInput(){
		return '<input type="hidden" name="'.parent::getName().'[value]" value="'.$this->getValue().'" />';
	}

	protected function _getUrl(){
		return $this->getValue();
	}

	public function getName(){
		return $this->getData('name');
	}
}