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

class Brander_AutoImport_Helper_Report extends Brander_AutoImport_Helper_Data
{

    public function getReportFilenames()
    {
        if (self::$_reportFilenames) {
            return self::$_reportFilenames;
        }

        if ($task = Mage::registry('importgrid_data')) {
            if ($task->getReportFilenames() == null) {
                return array();
            }
            return json_decode($task->getReportFilenames());
        }

        $importLogDir = Mage::getBaseDir('var').DS.self::$_reportFolder.DS;
        $this->chkDir($importLogDir);
        $logDateTime = $this->getImportDateTime();
        foreach ($this->getReportsTypes() as $type) {
            $this->setReportFilename($type, 'import'.'_'.$type.'_'.$logDateTime.'.'.self::$_reportFileExt);
        }
        return self::$_reportFilenames;
    }

    protected function setReportFilename($type, $name)
    {
        self::$_reportFilenames[$type] = $name;
    }

    protected function getReportFilename($type)
    {
        $filenames = $this->getReportFilenames();
        return $filenames[$type];
    }

    public function saveProductsReport($data, $type)
    {

        if (in_array($type, $this->getReportsTypes())) {
            $filename = $this->getReportFolder().DS.$this->getReportFilename($type);

            $xmlObj = new Varien_Convert_Parser_Xml_Excel();
            $xmlObj->setVar('single_sheet', $type.'_products');
            $xmlObj->setData($data);
            $xmlObj->unparse();
            $content=$xmlObj->getData();

            $handle = fopen($filename, 'a+');
            fwrite($handle, $content);
            fclose($handle);
            self::$_reportFilenames[$type] = $this->getReportFilename($type);
        }
    }

    public function getReportFolder()
    {
        return 'var'.DS.self::$_reportFolder;
    }

    public function getReportDirPath()
    {
        return Mage::getBaseDir('var').DS.self::$_reportFolder;
    }

    public function getReportDirUrl()
    {
        return Mage::getBaseDir('var').DS.self::$_reportFolder.DS;
    }
}
