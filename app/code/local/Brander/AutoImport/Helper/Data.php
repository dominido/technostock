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

class Brander_AutoImport_Helper_Data extends Mage_Core_Helper_Abstract
{

    static $_logFilenames       = null;
    static $_logFolder          = 'import';

    static $_reportFilenames    = array();
    static $_reportFolder       = 'log/import';
    static $_reportFileExt      = 'xls';

    static $_importDateTime     = null;
    static $_importFolder       = 'import';
    static $_importFiles        = array();

    // process types
    const TYPE_IMPORT           = 'import';
    const TYPE_IMPORT_LOG       = 'log';

    // message types
    const TYPE_MESSAGE_LOG      = 'log';
    const TYPE_ERROR_LOG        = 'error';

    const TYPE_MESSAGE_NEW      = 'new';
    const TYPE_MESSAGE_UPDATE   = 'update';
    const TYPE_MESSAGE_OLD      = 'out_of_stock';
    const TYPE_MESSAGE_REMOVE   = 'remove';

    public $logsTypes = array(self::TYPE_MESSAGE_LOG, self::TYPE_ERROR_LOG);
    public $reportsTypes = array(self::TYPE_MESSAGE_NEW, self::TYPE_MESSAGE_UPDATE, self::TYPE_MESSAGE_OLD, self::TYPE_MESSAGE_REMOVE);


    /*
     * AutoImport Config params
     */
    public function getImportConfig($paramName = 'brander_auto_import/general', $storeId = null)
    {
        $configOption = Mage::getStoreConfig($paramName, $storeId);
        if (is_array($configOption)) {
            $config = new Varien_Object($configOption);
            return $config;
        }
        return $configOption;
    }

    public function chkDir($dir)
    {
        if (!is_dir($dir)) {
            mkDir($dir);
        }
    }

    public function getImportDateTime()
    {
        if (!self::$_importDateTime) {
            self::$_importDateTime = date('Y-m-d-H-i');
        }
        return self::$_importDateTime;
    }

    public function convertTimeGmtToNow($time) {
        $currentTimezone = Mage::getStoreConfig('general/locale/timezone');
        $task_datetime = new Zend_Date();
        $task_datetime->setTimezone('GMT');
        $task_datetime->set($time);
        $task_datetime->setTimezone($currentTimezone);

        return $task_datetime->get('YYYY-MM-dd HH:mm:ss');
    }

    public function convertTimeNowToGmt($time) {
        $currentTimezone = Mage::getStoreConfig('general/locale/timezone');
        $task_datetime = new Zend_Date();
        $task_datetime->setTimezone($currentTimezone);
        $task_datetime->set($time);
        $task_datetime->setTimezone('GMT');

        return $task_datetime->get('YYYY-MM-dd HH:mm:ss');
    }

    public function getReportsTypes()
    {
        return $this->reportsTypes;
    }

    public function getLogsTypes()
    {
        return $this->logsTypes;
    }

	public function checkForEditMode()
	{
		if (Mage::registry('importgrid_data') && Mage::registry('importgrid_data')->getId()) {
			return true;
		}
			return false;
	}

    public function getImportStartMinutesPeriod()
    {
        return 60;
    }

    public function getImportFolder()
    {
        return Mage::getBaseDir('var').DS.self::$_importFolder;
    }

    public function getClientIp()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else {
            if (getenv('HTTP_FORWARDED_FOR')) {
                $ipaddress = getenv('HTTP_FORWARDED_FOR');
            } else {
                if (getenv('HTTP_FORWARDED')) {
                    $ipaddress = getenv('HTTP_FORWARDED');
                } else {
                    if (getenv('REMOTE_ADDR')) {
                        $ipaddress = getenv('REMOTE_ADDR');
                    } else {
                        $ipaddress = 'UNKNOWN';
                    }
                }
            }
        }
        return $ipaddress;
    }
}
