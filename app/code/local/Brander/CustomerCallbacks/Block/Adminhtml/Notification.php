<?php
/**
 * Brander CustomerCallbacks extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        CustomerCallbacks
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_CustomerCallbacks_Block_Adminhtml_Notification extends Mage_Adminhtml_Block_Template
{

    public function checkForNewCallbacks()
    {
        $newCallbacks = Mage::getModel('brander_customercallbacks/callbacks')
            ->getCollection()
            ->addFieldToFilter('notify_admin', 1);
        if ($count = $newCallbacks->getSize()) {
            return $count;
        }
        return false;
    }

    public function getManageUrl()
    {
        return $this->getUrl('adminhtml/callbacks/index');
    }

    public function setNotifiedStatus()
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $table = $resource->getTableName('brander_customercallbacks/callbacks');

        $query = "UPDATE {$table} SET `notify_admin` = 0;";
        $writeConnection->query($query);
    }
}