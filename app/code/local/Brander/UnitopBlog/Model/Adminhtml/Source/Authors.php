<?php
/**
 * Brander UnitopBlog extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        UnitopBlog
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_UnitopBlog_Model_Adminhtml_Source_Authors extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

	public function toOptionArray($addEmpty = true)
	{

		$selectedRoles = explode(',', Mage::helper('brander_shop')->getCfg('brander_unitopblog/settings/author_roles_select'));

		$collectionRoles = Mage::getModel('admin/roles')
			->getCollection()
			->addFieldToFilter('role_id', array('eq' => $selectedRoles));

		$authors = array();

		if ($addEmpty) {
			$authors[] = array(
				'label' => Mage::helper('adminhtml')->__('-- Custom Author Name --'),
				'value' => ''
			);
		}

		foreach ($collectionRoles as $role) {

			$users = Mage::getModel('admin/roles')->load($role->getRoleId())->getRoleUsers();

			if (count($users)) {
				foreach ($users as $_user) {
					$user = Mage::getModel('admin/user')->load($_user);
					$authors[] = array(
						'label' => $user->getFirstname() . ' ' . $user->getLastname(),
						'value' => $user->getUserId());
				}
			}
		}

		return $authors;
	}

	public function getAllOptions()
	{
		return $this->toOptionArray();
	}
}