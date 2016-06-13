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

class Brander_AutoImport_Helper_Stages extends Mage_Core_Helper_Abstract
{
    protected $_stage3downtime = 7;

    /*
     * AutoImport Stages Config params
     */
    public function getStagesConfig($storeId = null)
    {
        $configOption = Mage::getStoreConfig('brander_auto_import/stages', $storeId);
        if (is_array($configOption)) {
            $config = new Varien_Object($configOption);
            return $config;
        }
        return $configOption;
    }


    // TODO::::::::
    public function getStageThreeDates()
    {
        $config = $this->getStagesConfig();
        $dates = new Varien_Object();
        $to = $config->getData('stage3') - $config->getData('stage2');
        $dates->setStageToDate($this->getStageDate($to));
        $dates->setStageGridToDate($this->getStageDate($to - $this->_stage3downtime));
        return $dates;
    }

    public function getStageTwoDates()
    {
        $config = $this->getStagesConfig();
        $dates = new Varien_Object();
        $from = $config->getData('stage2') - $config->getData('stage1');
        $dates->setStageFromDate($this->getStageDate($config->getData('stage2')));
        $dates->setStageGridFromDate($this->getStageDate($from));
        return $dates;
    }

    public function getStagePeriod($stage)
    {
        $config = $this->getStagesConfig();
        $stageField = 'stage'.$stage;
        if (!$stage) {
            return $this->getStageDate(0);
        }
        return $this->getStageDate($config->getData($stageField));
    }

    protected function getStageDate($stagePeriod)
    {
        $stageDate  = Mage::app()->getLocale()->date()
            ->setDate(date('d-m-Y', strtotime(now() .' -'.$stagePeriod.' days')))
            ->setTime('00:00:00')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        return $stageDate;
    }

    public function getEnabled()
    {
        if ($this->getStagesConfig()->getEnableStages() && $this->getStagesConfig()->getEnableRemoval()) {
            return true;
        }
        return false;
    }
}
