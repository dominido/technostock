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

class Brander_AutoImport_Model_Source_Filetype
{

    const FILE_TYPE_AUTO_LOAD = '1';
    const FILE_TYPE_MANUAL_LOAD = '2';
    const FILE_TYPE_LOADED = '3';

    public function toOptionArray()
    {
        $statuses = $this->getStatuses();
        foreach ($statuses as $key => $status) {
            $arrayOption[$key] = array('value'=>$key, 'label'=>$status);
        }

        return $arrayOption;
    }

    public function getStatuses()
    {
        $statuses = array(
            '0' => Mage::helper('autoimport')->__('-- please select --'),
            Brander_AutoImport_Model_Source_Filetype::FILE_TYPE_AUTO_LOAD => Mage::helper('autoimport')->__('auto upload file(s) (via FTP)'),
            Brander_AutoImport_Model_Source_Filetype::FILE_TYPE_MANUAL_LOAD => Mage::helper('autoimport')->__('upload file(s) now ...'),
            Brander_AutoImport_Model_Source_Filetype::FILE_TYPE_LOADED => Mage::helper('autoimport')->__('file(s) already uploaded'),
        );
        return $statuses;
    }

}
