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

class Brander_AutoImport_Model_Source_Importmode
{

    const IMPORT_FILE_EXTENSION_CSV = 1;
    const IMPORT_FILE_EXTENSION_XML = 2;

    public function toOptionArray()
    {
        $statuses = $this->getStatuses();
        $arrayOption = array();
        foreach ($statuses as $key => $status) {
            $arrayOption[$key] = array('value'=>$key, 'label'=>$status);
        }
        return $arrayOption;
    }

    public function getStatuses()
    {
        $statuses = array(
            '0' => '',
            self::IMPORT_FILE_EXTENSION_CSV => Mage::helper('autoimport')->__('CSV'),
            self::IMPORT_FILE_EXTENSION_XML => Mage::helper('autoimport')->__('CommerceML XML'),
        );
        return $statuses;
    }

}
