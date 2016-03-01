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
class ET_SocialLogin_Model_CustomerGroups extends Varien_Object
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('et_sociallogin');
        $collection = Mage::getModel('customer/group')->getCollection();

        $groups = array();
        $groups[] = array('value' => '', 'label' => $helper->__('-- Please Select --'));
        foreach ($collection as $group) {
            if ($group->getCustomerGroupId() != 0) {
                $groups[] = array('value' => $group->getCustomerGroupId(), 'label' => $group->getCustomerGroupCode());
            }
        }
        return $groups;
    }
}
