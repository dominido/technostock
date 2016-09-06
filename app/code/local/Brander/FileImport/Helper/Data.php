<?php 
/**
 * Brander_FileImport extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Brander
 * @package		Brander_FileImport
 * @copyright  	Copyright (c) 2014
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * FileImport default helper
 *
 * @category	Brander
 * @package		Brander_FileImport
 * @author Ultimate Module Creator
 */
class Brander_FileImport_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function checkForEditMode()
	{
		if (Mage::registry('filegrid_data') && Mage::registry('filegrid_data')->getId()) {
			return true;
		}
		else {
			return false;
		}
	}

	public function getExportUrl($id, $store = array()) {

		$pathUrl = Mage::getUrl(
			'price/get/file',
			array('id' => $id, '_type' => Mage_Core_Model_Store::URL_TYPE_WEB)
		);
		if (Mage::getStoreConfig('web/url/use_store')) {
			if (count($store) == 0 || $store[0] == '0') {
				$storeId = Mage::app()
					->getWebsite(true)
					->getDefaultGroup()
					->getDefaultStoreId();
			}
			else {
				$storeId = $store[0];
			}
			$storePath = Mage::getModel('core/store')->load($storeId);
			$pathUrl = str_replace('export/get', $storePath->getCode() . DS . 'export/get', $pathUrl);
		}

		return $pathUrl;
	}

	public function getExportDir() {
		$_exportDir = '/';
		return $_exportDir;
	}

	public function getFullExportDir() {
		$_exportDir = Mage::getBaseDir() . DS . 'media/files';
		return $_exportDir;
	}

	public function getFilePath($filePrice) {
		$file = Mage::getBaseDir().DS.'media/files'.DS.$filePrice->getFileName();
		return $file;
	}
}