<?php
/**
 * Brander Account extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        Account
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_Account_Block_Navigation extends Mage_Customer_Block_Account_Navigation
{
	public function removeLinkByName($name)
	{
		foreach ($this->_links as $k => $v) {
			if ($v->getName() == $name) {
				unset($this->_links[$k]);
			}
		}

		return $this;
	}

	protected function _beforeToHtml()
	{
		$this->_links;
		$this->removeLinkByName('recurring_profiles');
		$this->removeLinkByName('OAuth Customer Tokens');
		$this->removeLinkByName('downloadable_products');
		$this->removeLinkByName('billing_agreements');
		$this->removeLinkByName('tags');

		return $this;
	}

}