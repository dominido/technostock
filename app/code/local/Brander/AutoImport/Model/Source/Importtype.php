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

class Brander_AutoImport_Model_Source_Importtype
{

    const IMPORT_TYPE_CRON_MODE = 1;
    const IMPORT_TYPE_MANUAL_MODE = 2;

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
            self::IMPORT_TYPE_CRON_MODE => Mage::helper('autoimport')->__('Cron mode'),
            self::IMPORT_TYPE_MANUAL_MODE => Mage::helper('autoimport')->__('Manual mode'),
        );
        return $statuses;
    }

}
