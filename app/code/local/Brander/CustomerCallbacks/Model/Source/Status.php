<?php
/**
 * Brander CustomerCallback extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        CustomerCallback
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_CustomerCallbacks_Model_Source_Status
{

    const CALLBACK_STATUS_NEW = 0;
    const CALLBACK_STATUS_PROCESSED = 1;
    const CALLBACK_STATUS_BAD = 2;

    public function toOptionArray($addEmpty = true)
    {
        $options = array(
            array(
                'label' => Mage::helper('brander_customercallbacks')->__('New'),
                'value' => self::CALLBACK_STATUS_NEW
            ),
            array(
                'label' => Mage::helper('brander_customercallbacks')->__('Processed'),
                'value' => self::CALLBACK_STATUS_PROCESSED
            ),
            array(
                'label' => Mage::helper('brander_customercallbacks')->__('Fail'),
                'value' => self::CALLBACK_STATUS_BAD
            )
        );

        return $options;
    }

    public function getStatuses($arrayOption = array())
    {
        foreach ($this->toOptionArray() as $status) {
            $arrayOption[$status['value']] = $status['label'];
        }
        return $arrayOption;
    }

}