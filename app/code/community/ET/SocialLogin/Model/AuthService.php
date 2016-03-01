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

class ET_SocialLogin_Model_AuthService extends Mage_Core_Model_Abstract
{

    protected $_config;
    protected $_connect;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('et_sociallogin/authService');
        $this->_config = Mage::helper('et_sociallogin')->getActiveSocialAccounts();

        $this->prepareConfig();

    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    protected function prepareConfig()
    {
        foreach ($this->_config as $key => &$item) {
            $item['callback'] = Mage::getUrl(Mage::getStoreConfig('social_auth_callback'), array('provider' => $key));
            $item['adapter'] = Mage::getConfig()->getModelClassName('et_sociallogin/authAdapter_' . $key);
        }
    }

    public function getConfigs()
    {
        return $this->_config;
    }

    public function getConnect($provider, $responsetype = false)
    {
        if (!$this->_connect) {

            if (!isset($this->_config[$provider])) {
                throw new ET_SocialLogin_Model_SocialException('provider not exists');
            }

            $options = $this->_config[$provider];

            if (!class_exists($options['adapter'])) {
                throw new ET_SocialLogin_Model_SocialException('adapter not exists');
            }

            if ($responsetype && !in_array($responsetype, array('popup', 'iframe', 'redirect'))) {
                throw new ET_SocialLogin_Model_SocialException('Bad request');
            }

            $this->_connect = new ET_SocialLogin_Model_Oauth($options);
        }

        return $this->_connect;
    }

    /**
     * @param $provider
     * @param $params
     * @return ET_SocialLogin_Model_AuthResult
     * @throws ET_SocialLogin_Model_SocialException
     */
    public function callback($params)
    {
        $provider = isset($params['provider']) ? $params['provider'] : false;
        if (!$provider) {
            $result = new ET_SocialLogin_Model_AuthResult(ET_SocialLogin_Model_AuthResult::AUTHORIZE_FAILED);
            $result->setMessage(Mage::helper('core')->__('Bad Request'));
            return $result;
        }

        $_connect = $this->getConnect($provider);
        $callback = $_connect->getCallback($params);


        if (is_array($callback) && array_key_exists('error', $callback)) {
            throw new ET_SocialLogin_Model_SocialException(Mage::helper('core')->__($callback['error_description']));
        }

        if (!$callback) {
            $result = new ET_SocialLogin_Model_AuthResult(ET_SocialLogin_Model_AuthResult::AUTHORIZE_FAILED);

            $helper = Mage::helper('core');
            $result->setMessage($helper->__('Невозможно получить ответ от сервера'));
            return $result;
        }
        $userinfo = $_connect->getUserInfo($callback);

        if (!$userinfo) {
            $result = new ET_SocialLogin_Model_AuthResult(ET_SocialLogin_Model_AuthResult::AUTHORIZE_FAILED);

            $result->setMessage(Mage::helper('core')->__('Ошибка авторизации'));
            return $result;
        }

        $mappedUserinfo = $_connect->getMappedUserInfo($userinfo);

        $socialCustomer = $this->findSocialCustomer($mappedUserinfo['userid'], $provider);


        if ($this->_getSession()->isLoggedIn()) {

            $this->_getSession()->setSocialData($mappedUserinfo);
            return new ET_SocialLogin_Model_AuthResult(ET_SocialLogin_Model_AuthResult::LINK_EXISTS_CUSTOMER);
        }

        if ($socialCustomer) {
            $this->authorize($socialCustomer);

            $result = new ET_SocialLogin_Model_AuthResult(ET_SocialLogin_Model_AuthResult::AUTHORIZE_SUCCESS);
            $result->setSocialAuthResponseType($this->_getSession()->getData('social_auth_response_type'));
            return $result;
        } else {
            $this->_getSession()->setSocialData($mappedUserinfo);
            return new ET_SocialLogin_Model_AuthResult(ET_SocialLogin_Model_AuthResult::REDIRECT_TO_REGISTRATION);
        }
    }


    /**
     * @param $providerUserId
     * @param $provider
     * @return bool
     */
    public function findSocialCustomer($providerUserId, $provider)
    {

        $socialCustomer = Mage::getModel('et_sociallogin/socialCustomer')
            ->getCollection()
            ->addFieldToFilter('social_customer_id', $providerUserId)
            ->addFieldToFilter('social_provider', $provider)
            ->getFirstItem();


        if ($socialCustomer && $socialCustomer->getCustomerId()) {
            return $socialCustomer;
        } else {
            return false;
        }
    }

    /**
     * Авторизация кастомера
     * @param $socialCustomer
     * @return bool
     */

    protected function authorize(ET_SocialLogin_Model_SocialCustomer $socialCustomer)
    {
        $customer = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
        $customer->load($socialCustomer->getCustomerId());

        if (!$customer->getId()) {
            return false;
        }

        $this->_getSession()->setCustomerAsLoggedIn($customer);
        if (method_exists($this->session, 'renewSession')) {
            $this->_getSession()->renewSession();
        }
        return true;
    }


    /**
     * Привязка аккаунта к существующему кастомеру
     * @param $params
     * @return ET_SocialLogin_Model_AuthResult
     */
    public function mergeAccount($customer, $customerSocialData)
    {

        $data = $customer->getData();
        $data = array_merge($data, $customerSocialData);

        $customer->addData($data);
        $customer->save();
        $this->linkSocialCustomer($customer, $customerSocialData);

        $result = new ET_SocialLogin_Model_AuthResult(ET_SocialLogin_Model_AuthResult::REGISTRATION_SUCCESS);
        $result->setSocialAuthResponseType($this->_getSession()->getData('social_auth_response_type'));
        return $result;
    }


    /**
     * Добавляет к кастомеру, запись о привязки к соц.сети
     * @param $customer
     * @param $userinfo
     * @return false|Mage_Core_Model_Abstract
     */
    protected function linkSocialCustomer($customer, $userinfo)
    {
        $socialCustomer = Mage::getModel('et_sociallogin/socialCustomer');
        $socialCustomer->setSocialCustomerId($userinfo['userid']);
        $socialCustomer->setCustomerId($customer->getId());
        $socialCustomer->setSocialProvider($userinfo['provider']);
        $socialCustomer->setSocialProfileLink($userinfo['link']);
        $socialCustomer->setSocialName($userinfo['username']);
        $socialCustomer->setSocialPhoto($userinfo['photo']);
        $socialCustomer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());

        $socialCustomer->save();

        return $socialCustomer;
    }
}
