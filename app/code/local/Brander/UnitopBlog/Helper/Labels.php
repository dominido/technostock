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
class Brander_UnitopBlog_Helper_Labels extends Mage_Core_Helper_Abstract
{
	/**
	 * Get product labels (HTML)
	 *
	 * @return string
	 */
	public function getLabels(Varien_Object $object)
	{
		$html = '';

		$isNew = false;

		$isPopular = false;

		if ($isNew == true)
		{
			$html .= '<span class="sticker-wrapper top-left"><span class="sticker new">' . $this->__('New') . '</span></span>';
		}

		if ($isPopular == true)
		{
			$html .= '<span class="sticker-wrapper top-right"><span class="sticker sale">' . $this->__('Popular') . '</span></span>';
		}

		return $html;
	}

	/**
	 * Check if "new" label is enabled and if product is marked as "new"
	 *
	 * @return  bool
	 */
	public function isNew($object)
	{
		$date = new Zend_Date(Mage::getModel('core/date')->timestamp());
		$date->addDay('-7');
		//$date->toString('Y-MM-d H:m:s');
		if ($object->getCreatedAt() > $date->toString('Y-MM-d H:m:s')) {
			return true;
		}
		return false;
	}

	/**
	 * Check if "popular" label is enabled
	 *
	 * @return  bool
	 */
	public function isPopular($object)
	{
		return false;
	}

}
