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

class Brander_AutoImport_Block_Adminhtml_Importgrid_Renderer_FileType extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $rowIndex = $row->getData($this->getColumn()->getIndex());

        if ($rowIndex == Brander_AutoImport_Model_Source_Filetype::FILE_TYPE_AUTO_LOAD) {
            $title = '<div><b>Auto load via FTP</b></div>';
            $title .= $this->getFileDetails($row);
        }
        elseif ($rowIndex == Brander_AutoImport_Model_Source_Filetype::FILE_TYPE_MANUAL_LOAD) {
            $title = '<div><b>Manual file uploaded</b></div>';
            $title .= $this->getFileDetails($row);
        }
        elseif ($rowIndex == Brander_AutoImport_Model_Source_Filetype::FILE_TYPE_LOADED) {
            $title = '<div><b>Manual file get in folder</b></div>';
            $title .= $this->getFileDetails($row);
        }
        else {
            $title = '';
        }

        return $title;
    }

    protected function getFileDetails($row)
    {
        // TODO:::
        return '';
        $filesData = $row->getImportFilesData();
        $title = '';

        foreach ($filesData as $fileData) {
            $title .= '<div>___________________</div>';
            $title .= '<div>Import File:</div>';
            $title .= '<div>Name: ' . ($row->getImportFilesData() ? $row->getImportFilename() : 'no data') . '</div>';
            $title .= '<div>Size: ' . ($row->getImportFileSize() ? $row->getImportFileSize() : 'no data') . '</div>';
            $title .= '<div>Loaded: ' . ($row->getImportFileLoadtime() ? date('Y-m-d H:i' ,Mage::getModel('core/date')->timestamp($row->getImportFileLoadtime())) : 'no data') . '</div>';
        }

        return $title;
    }
}