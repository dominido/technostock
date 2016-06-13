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

class Brander_AutoImport_Model_Import extends Varien_Object
{
    static    $_currentTask     = null;
    static    $_importTime      = null;
    static    $_importTimeGmt   = null;

    const     TASK_STATUS_SCHEDULED         = 'scheduled';
    const     TASK_STATUS_STARTED           = 'started';
    const     TASK_STATUS_IN_PROGRESS       = 'in progress ...';
    const     TASK_STATUS_REINDEXING        = 'reindexing ...';
    const     TASK_STATUS_REINDEX_COMPLETE  = 'reindex complete';
    const     TASK_STATUS_DUMPING_DB        = 'dumping db';
    const     TASK_STATUS_DUMP_DB_COMPLETE  = 'dump db complete';
    const     TASK_STATUS_DUMP_DB_FAIL      = 'dump db fail';
    const     TASK_STATUS_FINISHED          = 'finished';
    const     TASK_STATUS_FAILED            = 'failed';
    const     TASK_STATUS_FILE_CHECKED      = 'file checked';
    const     TASK_STATUS_FILE_CHECK_FAIL   = 'file check fail';
    const     TASK_STATUS_FILE_DOWNLOAD_SKIP   = 'skipped download files';
    const     TASK_STATUS_DOWNLOADING       = 'downloading files';

    protected $_stopListStatuses = array(
        self::TASK_STATUS_FAILED,
        self::TASK_STATUS_DUMP_DB_FAIL,
        self::TASK_STATUS_FILE_CHECK_FAIL,
        self::TASK_STATUS_FILE_DOWNLOAD_SKIP
    );

    protected $_importFiles     = array();
    protected $_dumpDbDir       = 'var/dumpdb';
    protected $_filetype        = null;



    public function cronImportStart()
    {
        // check for non complete tasks
        // check for scheduled tasks

        self::$_importTime = Mage::getModel('core/date')->timestamp(time());
        self::$_importTimeGmt = $this->getHelper()->convertTimeNowToGmt(date("Y-m-d H:i:s", self::$_importTime));

        $config = $this->getHelper()->getImportConfig();

        if ($config->getAutoimportEnable() == 0) {
            //module disabled, process skipped
            return true;
        }

        if (!$this->isAllowToStart()) {return false;}
        if (!$lastTask = self::$_currentTask) {return false;}

        $this->importStart();
        $this->getLogHelper()->logMessage('STATUS MESSAGE: Import process finished');
        return true;
    }

    protected function isAllowToStart()
    {
        $scheduledTasks = $this->getScheduledTasks();
        $startedTask = $this->getStartedTasks();
        $finishedTasks = $this->getFinishedLasHourTasks();

        if ($startedTask->getSize() || $finishedTasks->getSize()) {
            // TODO :: good check
            $this->getLogHelper()->logMessage('WARNING MESSAGE: Not started. Another task already running this hour');
            return false;
        }

        if ($scheduledTasks->getSize()) {
            foreach ($scheduledTasks as $scheduledTask) {
                if (strtotime($scheduledTask->getPlannedAt()) < strtotime(self::$_importTimeGmt))  {
                    self::$_currentTask = $scheduledTask;
                    $this->getLogHelper()->logMessage('START: scheduled task will start now');
                    return true;
                }
            }
            $this->getLogHelper()->logMessage('ABORT: you has other tasks scheduled this on this hour');
            return false;
        }

        // add new task for avtoimport process
        $autoTask = Mage::getModel('autoimport/importgrid');
        $config = $this->getHelper()->getImportConfig();

        $data['planned_at'] = $this->getHelper()->convertTimeNowToGmt(date("Y-m-d H:i:s", self::$_importTime));
        $data['import_status'] = self::TASK_STATUS_SCHEDULED;
        if ($config->getAutoimportGetfileLoad()) {
            $data['file_type'] = Brander_AutoImport_Model_Source_Filetype::FILE_TYPE_AUTO_LOAD;
        } else {
            $data['file_type'] = Brander_AutoImport_Model_Source_Filetype::FILE_TYPE_LOADED;
        }
        $data['import_type'] = Brander_AutoImport_Model_Source_Importtype::IMPORT_TYPE_CRON_MODE;



        try {
            $autoTask->addData($data);
            $autoTask->save();
            self::$_currentTask = $autoTask;
            return true;

        } catch (Mage_Core_Exception $e){
            Mage::logException($e);
            return false;
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
    }


    public function importStart()
    {
        $this->getLogHelper()->logMessage('process starts at: ' . $this->getImportStartTime());

        //$this->_config = $this->getHelper()->getImportConfig()->getData();
        $config = $this->getHelper()->getImportConfig();

        if (!$config->getAutoimportEnable()) {
            $this->getLogHelper()->logMessage('Check configuration, auto import has disable status', 'error');
            //$this->stopProcess();
            return 'Autoimport disabled';
        }

	    $this->updateImportGrid(self::TASK_STATUS_STARTED);
        $this->getLogHelper()->logMessage('You have access to import process');

        try {

            // do database backup
            if ($config->getAutoimportEnableAutodumpDb()) {
                $this->dumpDB();
            }

            // download import files

            if ($this->getFile()) {
                if ($this->checkFile()) {
                    if ($config->getAutoimportEnableAutostartImport()) {
                        $importResult = $this->importProcess();
                        if ($importResult == false) {
                            $this->getLogHelper()->logMessage('import is not complete. Check import file');
                        }
                        else {
                            if ($config->getAutoimportEnableDeleteImportfile()) {
                                $this->deleteLocalImportFile();
                            }
                        }
                        if ($config->getAutoimportEnableAutostartReindex()) {
                            $this->startReindex();
                        }
                    }
                    else {
                        $this->getLogHelper()->logMessage('check import configuration');
                    }
                } else {
                    $this->getLogHelper()->logMessage('fail check import files');
                    $currentTask = self::$_currentTask;
                    $currentTask->delete();
                    return false;
                }
            } else {
                $this->getLogHelper()->logMessage('fail download import files');
                $currentTask = self::$_currentTask;
                $currentTask->delete();
                return false;
            }
        } catch (Mage_Core_Exception $e) {
            $this->updateImportGrid(self::TASK_STATUS_FAILED);
            return false;
        }
        $this->getLogHelper()->logMessage('all process finished !!');
        $this->updateImportGrid(self::TASK_STATUS_FINISHED);
    }


	protected function checkFile()
    {
        foreach ($this->_importFiles as $importFile) {
            $filePath = $this->getHelper()->getImportFolder() . DS . $importFile['file_name'];
            if (is_file($filePath) && is_readable($filePath)) {
                $this->updateImportGrid(self::TASK_STATUS_FILE_CHECKED);
            } else {
                $this->updateImportGrid(self::TASK_STATUS_FILE_CHECK_FAIL);
                return false;
            }
        }
		return true;
	}

    protected function deleteLocalImportFile()
    {
        foreach ($this->_importFiles as $importFilename) {
            $fullFilePath = $this->getHelper()->getImportFolder() . DS . $importFilename['file_name'];
            if (is_file($fullFilePath) && is_readable($fullFilePath)) {
                unlink($fullFilePath);
                $this->getLogHelper()->logMessage('Success delete import source local file');
            }
            $this->getLogHelper()->logMessage('Fail delete import source local file');
        }
    }

    protected function getFile()
    {
        $config = $this->getHelper()->getImportConfig();
        $task = self::$_currentTask;

        if ($task->getFileType() != Brander_AutoImport_Model_Source_Filetype::FILE_TYPE_AUTO_LOAD) {
            return false;
        }

        if (!$config->getAutoimportGetfileLoad()) {
            $this->getLogHelper()->logMessage('using external source file disables. Check configuration');
            return false;
        }
        if (!$config->getAutoimportEnableAutoGetfile()) {
            $this->getLogHelper()->logMessage('get file via FTP connection disabled. Check configuration');
            return false;
        }
        if (!$config->getAutoimportGetfileHost() ||
            !$config->getAutoimportGetfileUsername() ||
            !$config->getAutoimportGetfileUserpass()) {
            $this->getLogHelper()->logMessage('check 1C server connection configuration');
            return false;
        }

        if ($this->_filetype == Brander_AutoImport_Model_Source_Filetype::FILE_TYPE_LOADED) {
            $this->getLogHelper()->logMessage('task has manual file upload configuration, FTP load file missed');
            $this->updateImportGrid('missed FTP file upload (manual loaded)');
            return false;
        }

        if ($this->_filetype == Brander_AutoImport_Model_Source_Filetype::FILE_TYPE_MANUAL_LOAD) {
            return false;
        }

        if (!$port = $config->getAutoimportGetfilePort()) {
            $port = 21;
        }

        if (!is_dir($this->getHelper()->getImportFolder())) {
            mkDir($this->getHelper()->getImportFolder());
        }
        setlocale(LC_ALL, "ru_RU.UTF-8");


        $getFileNames = explode(',', str_replace(' ', '', $config->getAutoimportGetfileFilenames()));

        foreach ($getFileNames as $getFileName) {

            $this->getLogHelper()->logMessage('import file downloading');
            $this->updateImportGrid(self::TASK_STATUS_DOWNLOADING);

            $url = "ftp://" .
                $config->getAutoimport_getfile_username() . ":" .
                $config->getAutoimport_getfile_userpass() . "@" .
                $config->getAutoimport_getfile_host() . ":" .
                $config->getAutoimport_getfile_port() . "/" .
                $config->getAutoimport_getfile_path() .
                $getFileName;

            $host = "ftp://" . $config->getAutoimportGetfileHost();

            $access = $config->getAutoimportGetfileUsername() . ":" . $config->getAutoimportGetfileUserpass();
            $remoteFile = $config->getAutoimportGetfilePath() . $getFileName;

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);

            if (!$result) {
                $this->getLogHelper()->logMessage('check 1C server connection configuration. Cant load file');
                curl_close($curl);
                return false;
            }

            $file = fopen($this->getHelper()->getImportFolder() . DS . $getFileName, "w");
            fwrite($file, $result);
            fclose($file);
            curl_close($curl);

            if ($config->getAutoimportDeleteFtpfile()) {
                try {
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $host);
                    curl_setopt($curl, CURLOPT_PORT, $port);
                    curl_setopt($curl, CURLOPT_USERPWD, $access);
                    curl_setopt($curl, CURLOPT_FILE, "/" . $remoteFile);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_QUOTE, array("DELE /" . $remoteFile));
                    curl_exec($curl);
                    curl_close($curl);
                    $this->getLogHelper()->logMessage('1C import file deleted: ' . $getFileName);
                } catch (Exception $e) {
                    $this->getLogHelper()->logMessage($e->getMessage());
                }
            }
            $fileData['file_name'] = $getFileName;
            $fileData['file_path'] = $remoteFile;
            $fileData['file_load_time'] = Mage::getSingleton('core/date')->gmtDate();
            $fileData['file_size'] = '1.3Mb';

            $this->_importFiles[] = $fileData;
        }
        $this->getLogHelper()->logMessage('file download from FTP and save complete');
        $this->updateImportGrid('import file dowloaded from FTP');
        return true;
    }

    protected function importProcess()
    {
        $limit = ini_get('memory_limit'); ini_set('memory_limit', '-1');
	    $this->updateImportGrid(self::TASK_STATUS_IN_PROGRESS);

        //CommerceML XML format
        $import = Mage::getModel('brandercml/import')
            ->setSourceFile('komtek-import.xml', 'catalog')
            ->setSourceFile('komtek-offers.xml', 'offers')
            ->setBasePriceName('Цена нал.')
            ->setSpecialPriceName('Нал. со скидкой')
            ->setConsoleLog(true)
            ->setLogFile($this->getLogHelper()->getLogFilename())
            ->clearCatalog(false)
            ->run()
        ;

	    $this->updateImportGrid('Product Import Finished');

        ini_set('memory_limit', $limit);
        $this->getLogHelper()->logMessage('COMPLETE');

        return true;
    }

    public function startReindex()
    {
        $this->getLogHelper()->logMessage('START REINDEX ALL');
	    $this->updateImportGrid(self::TASK_STATUS_REINDEXING);
        $indexCollection = Mage::getModel('index/process')->getCollection();
        foreach ($indexCollection as $index) {
            $index->reindexAll();
        }

        try {
            $this->getLogHelper()->logMessage('START UPDATE PRO LABELS');
            $this->updateImportGrid('updating pro labels ...');
            $labelsModel = Mage::getResourceModel('prolabels/label');
            if ($labelsModel) {
                $labelsModel->deleteAllLabelIndex();
                $labelsModel->reindexAllSystemLabels();
                $labelModel = Mage::getModel('prolabels/label');
                $labelModel->applyAll();
            }
            $this->getLogHelper()->logMessage('COMPLETE UPDATE PRO LABELS');
        } catch (Exception $e) {
            $this->getLogHelper()->logMessage($e->getMessage());
        }

        $this->getLogHelper()->logMessage('REINDEX COMPLETE');
	    $this->updateImportGrid(self::TASK_STATUS_REINDEX_COMPLETE);
        return true;
    }

    protected function dumpDB()
    {
	    $this->updateImportGrid(self::TASK_STATUS_DUMPING_DB);
        $this->getLogHelper()->logMessage('START DB BACK-UP PROCESS');
        //error_reporting(E_ALL ^ E_NOTICE);
        //ini_set('display_errors', 1);
        //ini_set('memory_limit', '512M');

        // get Magento config
        $configDb  = Mage::getConfig()->getResourceConnectionConfig("default_setup");

        $dbconfig = array(
            'db_host' => $configDb->host,
            'db_user' => $configDb->username,
            'db_pass' => $configDb->password,
            '$db_name' => $configDb->dbname,
        );
        $db_host = $dbconfig['db_host'];
        $db_user = $dbconfig['db_user'];
        $db_pass = $dbconfig['db_pass'];
        $db_name = $dbconfig['$db_name'];

        if (!is_dir($this->_dumpDbDir)) {
            mkDir($this->_dumpDbDir);
        }

        // filename
        $backup_file = $this->_dumpDbDir . DS . $db_name ."-". date("Y-m-d-H-i-s") . ".gz";
        $command = "mysqldump --database " . $db_name  . " -u ". $db_user  . " -p'". $db_pass . "' | gzip > " . $backup_file;
        $this->getLogHelper()->logMessage('command executing is ' . "mysqldump --database " . $db_name  . " -u **** -p ***** | gzip > " . $backup_file, false);

        $output = shell_exec($command);

        if ($backup_file && file_exists($backup_file)) {
            $this->getLogHelper()->logMessage('BACK-UP DB SAVE TO FILE: file is ' . $backup_file);
	        $this->updateImportGrid(self::TASK_STATUS_DUMP_DB_COMPLETE);
        }
        else {
            $this->getLogHelper()->logMessage('DATABASE BACK-UP SAVE FAIL');
	        $this->updateImportGrid(self::TASK_STATUS_DUMP_DB_FAIL);
        }
        $this->getLogHelper()->logMessage('DATABASE BACK-UP SAVED');
    }

	protected function updateImportGrid($status)
    {
        $importgrid = self::$_currentTask;
		$now = Mage::getSingleton('core/date')->gmtDate();

        if ($status == self::TASK_STATUS_STARTED) {
			$data['log_filename'] = json_encode($this->getLogHelper()->getLogFilename());
			$data['start_at'] = $now;
			$data['import_status'] = $status;
		} elseif ($status == self::TASK_STATUS_FINISHED) {
            $data['finish_at'] = $now;
            $data['log_items_stat'] = Mage::registry('item_statuses');
            $data['report_filenames'] = json_encode($this->getReportHelper()->getReportFilenames());
        }

        $currentStatus = $importgrid->getImportStatus();

        if (!in_array($currentStatus, $this->_stopListStatuses)) {
            $data['import_status'] = $status;
        }

        try {
            $importgrid->addData($data);
            $importgrid->save();
            self::$_currentTask = $importgrid;
            return true;
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
	}

    protected function getScheduledTasks()
    {
        $collection = Mage::getModel('autoimport/importgrid')->getResourceCollection()
            ->addFieldToFilter('import_status', self::TASK_STATUS_SCHEDULED)
            ->addFieldToFilter('import_type', Brander_AutoImport_Model_Source_Importtype::IMPORT_TYPE_MANUAL_MODE)
            ->addFieldToFilter('planned_at', array('notnull' => true))
            ->addFieldToFilter('planned_at', array(array('date' => true, 'from' => $this->getStartFrom())))
            ->addFieldToFilter('planned_at', array(array('date' => true, 'to' => $this->getStartTo())))
            ->setOrder('planned_at', 'asc');
        return $collection;
    }

    protected function getStartedTasks()
    {
        $collection = Mage::getModel('autoimport/importgrid')->getResourceCollection()
            ->addFieldToFilter('start_at', array('notnull' => true))
            ->addFieldToFilter('finish_at', array('null' => true))
            ->addFieldToFilter('start_at', array(array('date' => true, 'from' => $this->getStartFrom())))
            ->setOrder('start_at', 'asc')
            ;
        return $collection;
    }

    protected function getFinishedLasHourTasks()
    {
        $collection = Mage::getModel('autoimport/importgrid')->getResourceCollection()
            ->addFieldToFilter('start_at', array('notnull' => true))
            ->addFieldToFilter('finish_at', array('notnull' => true))
            ->addFieldToFilter('start_at', array(array('date' => true, 'from' => $this->getStartFrom())))
            ->setOrder('start_at', 'asc')
        ;
        return $collection;
    }

    protected function getStartFrom()
    {
        $startPeriod = Mage::helper('autoimport')->getImportStartMinutesPeriod();
        $dateTime = date("Y-m-d H:i:s", strtotime("-".$startPeriod." minutes", self::$_importTime));
        return $this->getHelper()->convertTimeNowToGmt($dateTime);
    }

    protected function getStartTo()
    {
        $startPeriod = Mage::helper('autoimport')->getImportStartMinutesPeriod();
        $dateTime = date("Y-m-d H:i:s", strtotime("+".$startPeriod." minutes", self::$_importTime));
        return $this->getHelper()->convertTimeNowToGmt($dateTime);
    }

    protected function getImportStartTime()
    {
        // returns current server locale DateTime
        return date("Y-m-d H:i:s", self::$_importTime);
    }

    protected function getHelper()
    {
        return Mage::helper('autoimport');
    }

    protected function getLogHelper()
    {
        return Mage::helper('autoimport/log');
    }

    protected function getReportHelper()
    {
        return Mage::helper('autoimport/report');
    }

}