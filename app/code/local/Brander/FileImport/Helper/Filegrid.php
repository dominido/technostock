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
 * File Grid helper
 *
 * @category	Brander
 * @package		Brander_FileImport
 * @author Ultimate Module Creator
 */
class Brander_FileImport_Helper_Filegrid extends Mage_Core_Helper_Abstract{
	/**
	 * get base files dir
	 * @access public
	 * @return string
	 * @author Ultimate Module Creator
	 */
	public function getFileBaseDir()
    {
        $path = Mage::getBaseDir() . DS . 'media/files';
        if (!is_dir($path)) {
            mkdir($path);
        }
		return $path;
	}
	/**
	 * get base file url
	 * @access public
	 * @return string
	 * @author Ultimate Module Creator
	 */
	public function getFileBaseUrl(){
		return Mage::getBaseUrl().DS;
	}
	/**
	 * check if breadcrumbs can be used
	 * @access public
	 * @return bool
	 * @author Ultimate Module Creator
	 */
	public function getUseBreadcrumbs(){
		return Mage::getStoreConfigFlag('fileimport/filegrid/breadcrumbs');
	}

    public function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}