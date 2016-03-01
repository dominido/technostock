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

class ET_SocialLogin_Block_Adminhtml_SocialAccountGrid extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_socialAccountGrid';
        $this->_blockGroup = 'et_sociallogin';
        parent::__construct();
    }
}