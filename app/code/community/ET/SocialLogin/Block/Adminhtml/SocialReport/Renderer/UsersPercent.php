<?php

/**
 * NOTICE OF LICENSE
 *
 * You may not give, sell, distribute, sub-license, rent, lease or lend
 * any portion of the Software or Documentation to anyone.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_SocialLogin
 * @copyright  Copyright (c) 2015 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */
class ET_SocialLogin_Block_Adminhtml_SocialReport_Renderer_UsersPercent extends
    Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{

    public function _getValue(Varien_Object $row)
    {

        $usersAmount = $row->getData('users_amount');
        $usersTotal = Mage::registry('users_amount_total');

        if ($usersAmount != 0 && $usersTotal != 0) {
            $percent = round($usersAmount / $usersTotal * 100, 2) . '%';
            return $percent;
        } else {
            return 0;
        }
    }

}