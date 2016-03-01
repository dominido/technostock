<?php
/**
 * Brander Sha1 extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        Sha1
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_Sha1_Model_Encryption extends Mage_Core_Model_Encryption
{

    public function getHash($password, $salt = false)
    {
        if (is_integer($salt)) {
            $salt = $this->_helper->getRandomString($salt);
        }
        return $salt === false ? $this->hash($password) : $this->hash($password . $salt) . ':' . $salt;
    }

    public function  hash($data)
    {
        return sha1($data);
    }

    public function validateHash($password, $hash)
    {
        $hashArr = explode(':', $hash);
        switch (count($hashArr)) {
            case 1:
                return $this->hash($password) === $hash;
            case 2:
                return $this->hash($password . $hashArr[1]) === $hashArr[0];
        }
        Mage::throwException('Invalid hash.');
    }
}
