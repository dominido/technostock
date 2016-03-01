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
class Brander_Account_Block_DashboardAddress extends Mage_Customer_Block_Account_Dashboard_Address
{
	public function getDashboardAddress()
	{
		$address = $this->getCustomer()->getPrimaryBillingAddress();
		if (!$address) {
			return '';
		}
		$addressHtml = '';

		if ($postcode = $address->getPostcode()) {
			$addressHtml .= $postcode . ', ';
		}

		if ($region = $address->getRegion()) {
			$addressHtml .= $region . ', ';
		}

		if ($city = $address->getCity()) {
			$addressHtml .= $city . ', ';
		}

		if ($street = $address->getStreet()) {
			$addressHtml .= $street[0];
		}

		return $addressHtml;
	}
}