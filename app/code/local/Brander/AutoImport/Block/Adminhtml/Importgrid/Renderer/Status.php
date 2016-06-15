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

class Brander_AutoImport_Block_Adminhtml_Importgrid_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('autoimport');
        $rowIndex = $row->getData($this->getColumn()->getIndex());

/*        if ($rowIndex == 'complete' && $row->getLogItemsStat()) {

            $statsItems = jason_decode($row->getLogItemsStat());

            if (is_array($statsItems)) {

            }

            $stat = array(
                'new'          => '5',
                'update'       => '45',
                'out_of_stock' => '14',
            );
            $row->setLogItemsStat(json_encode($stat));
        }*/

        if ($rowIndex == Brander_AutoImport_Model_Import::TASK_STATUS_FINISHED) {
            $title = '<div>status: <b><span style="color:green">'.$rowIndex.'</span></b></div>';
            $importStats = $row->getLogItemsStat();
            if (!empty($importStats)) {
                $itemStats = json_decode($importStats);
                $title .= '<br />';
                $title .= 'Items Stat (count of):<br />';
                if (!empty($itemStats->new) || $itemStats->new == 0) {
                    $title .= $helper->__('New').': <b><span style="color:lawngreen">'. $itemStats->new .'</span></b><br />';
                }
                if (!empty($itemStats->update) || $itemStats->update == 0) {
                    $title .= $helper->__('Update').': <b><span style="color:orange">'. $itemStats->update .'</span></b><br />';
                }
                if (!empty($itemStats->out_of_stock) || $itemStats->out_of_stock == 0) {
                    $title .= $helper->__('Out of Stock').': <b><span style="color:orangered">'. $itemStats->out_of_stock .'</span></b><br />';
                }
                if (!empty($itemStats->remove) || $itemStats->remove == 0) {
                    $title .= $helper->__('Remove').': <b><span style="color:red">'. $itemStats->remove .'</span></b><br />';
                }
            }
        }


        elseif ($rowIndex == 'dumping db' || $rowIndex == 'dump db complete' || Brander_AutoImport_Model_Import::TASK_STATUS_REINDEXING) {
            $title = '<div>status: <b><span style="color:blue">'.$rowIndex.'</span></b></div>';
        }
        elseif ($rowIndex == Brander_AutoImport_Model_Import::TASK_STATUS_IN_PROGRESS) {
            $title = '<div>status: <b><span style="color:green">'.$rowIndex.'</span></b></div>';
        }
        elseif ($rowIndex == Brander_AutoImport_Model_Import::TASK_STATUS_SCHEDULED) {
            $title = '<div>status: <b><span style="color:grey">'.$rowIndex.'</span></b></div>';
        }
        elseif ($rowIndex == Brander_AutoImport_Model_Import::TASK_STATUS_FILE_CHECK_FAIL) {
            $title = '<div>status: <b><span style="color:red">'.$rowIndex.'</span></b></div>';
        }
        else {
            $title = '<div>status: <b><span>'.$rowIndex.'</span></b></div>';
        }
        return $title;
    }
}