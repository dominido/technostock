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
 * @copyright  Copyright (c) 2013 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */

class ET_SocialLogin_Model_AuthResult extends Varien_Object
{

    const AUTHORIZE_SUCCESS = 'AuthorizeSuccess';
    const REGISTRATION_SUCCESS = 'RegistrationSuccess';
    const REDIRECT_TO_REGISTRATION = 'RedirectToRegistration';
    const LINK_EXISTS_CUSTOMER = 'LinkExistsCustomer';
    const AUTHORIZE_FAILED = 'AuthorizeFailed';


    protected $_message;

    protected $_status;

    protected $_action;

    protected $_socialAuthResponseType;

    public function __construct($_status)
    {
        $this->_status = $_status;
    }


    public function getStatus()
    {
        return $this->_status;
    }

    public function setSocialAuthResponseType($socialAuhResponseType)
    {
        $this->_socialAuthResponseType = $socialAuhResponseType;
    }

    public function getSocialAuthResponseType()
    {
        return $this->_socialAuthResponseType;
    }


    public function getMessage()
    {
        return $this->_message;
    }

    public function setMessage($_message)
    {
        $this->_message = $_message;
    }

}
