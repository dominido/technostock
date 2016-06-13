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

class Brander_AutoImport_Model_Importgrid extends Mage_Core_Model_Abstract
{

	const ENTITY	= 'autoimport_importgrid';
	const CACHE_TAG = 'autoimport_importgrid';
	protected $_eventPrefix = 'autoimport_importgrid';
	protected $_eventObject = 'importgrid';

	public function _construct(){
		parent::_construct();
		$this->_init('autoimport/importgrid');
	}

	protected function _beforeSave(){
		parent::_beforeSave();
		$now = Mage::getSingleton('core/date')->gmtDate();
		if ($this->isObjectNew()){
            $this->setImportStatus('scheduled');
			$this->isObjectNew(false);
		}
		$this->setUpdatedAt($now);
		return $this;
	}

	protected function _afterSave() {
		return parent::_afterSave();
	}
}