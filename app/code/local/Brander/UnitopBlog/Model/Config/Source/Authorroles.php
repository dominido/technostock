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
class Brander_UnitopBlog_Model_Config_Source_Authorroles
{

	public function toOptionArray($addEmpty = true)
	{

		$collection = Mage::getModel('admin/roles')->getCollection();

		$roles = array();

		if ($addEmpty) {
			$roles[] = array(
				'label' => Mage::helper('adminhtml')->__('-- Please Select --'),
				'value' => ''
			);
		}

		foreach ($collection as $role) {
			$roles[] = array(
				'label' => $role->getRoleName(),
				'value' => $role->getId());
		}
		return $roles;
	}

	public function getAllOptions()
	{
		return $this->toOptionArray(false);
	}
}