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

class Brander_AutoImport_Helper_Log extends Brander_AutoImport_Helper_Data
{

    public function getLogFilenames()
    {
        if (self::$_logFilenames) {
            return self::$_logFilenames;
        }

        $importLogDir = Mage::getBaseDir('var').DS.self::$_logFolder.DS;
        $this->chkDir($importLogDir);

        $logDateTime = $this->getImportDateTime();
        foreach ($this->getLogsTypes() as $type) {
            $this->setLogFilename($type, 'import'.'_'.$type.'_'.$logDateTime.'.log');
        }

        //Mage::register('log_files', self::$_logFilenames);
        return self::$_logFilenames;
    }

    protected function setLogFilename($type, $name)
    {
        self::$_logFilenames[$type] = $name;
    }

    public function getLogFilename($type = 'log')
    {
        $filenames = $this->getLogFilenames();
        return $filenames[$type];
    }

    public function getLogFilePath($type = 'log')
    {
        $filenames = $this->getLogFilenames();
        if (isset($filenames[$type])) {
            return $this->getLogDirPath() . DS . $filenames[$type];
        }
        return '';
    }

    public function logMessage($message, $echo = true)
    {
        $message = '| '.date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())) . ' | ' . $message;
        Mage::log($message, null, self::$_logFolder.DS.$this->getLogFilename('log'));
        if ($echo == true) {
            echo ($message . PHP_EOL);
        }
    }

    public function errorMessage($message, $echo = true)
    {
        $message = '| '.date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())) . ' | ' . $message;
        Mage::log($message, null, self::$_logFolder.DS.$this->getLogFilename('error'));
        if ($echo) {
            echo ($message . PHP_EOL);
        }
    }

    public function getLogFolder()
    {
        return 'var'.DS.self::$_logFolder;
    }

    public function getLogDirPath()
    {
        return Mage::getBaseDir('var').DS.self::$_logFolder;
    }

    public function getLogDirUrl()
    {
        return Mage::getBaseDir('var').DS.self::$_logFolder.DS;
    }
}
