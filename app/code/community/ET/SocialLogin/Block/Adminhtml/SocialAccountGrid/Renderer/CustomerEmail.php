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

class ET_SocialLogin_Block_Adminhtml_SocialAccountGrid_Renderer_CustomerEmail
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render (Varien_Object $row)
    {
        $customer = Mage::getModel('customer/customer')->load($row->getCustomerId());

        return $customer->getEmail();
    }
}