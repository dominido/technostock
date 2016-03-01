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

/**
 * Адаптер Facebook
 */

class ET_SocialLogin_Model_AuthAdapter_Facebook extends ET_SocialLogin_Model_AuthAdapter_Abstract
{

    /**
     * Массив опций
     * @var array
     */
    protected $_options = array(
        'siteUrl' => 'https://graph.facebook.com',
        'authorizeUrl' => 'https://www.facebook.com/dialog/oauth',
        'requestTokenUrl' => 'https://graph.facebook.com/oauth/access_token',
        'userInfoUrl' => 'https://graph.facebook.com/me?access_token=%s',
        'response' => array('access_token'),
        'request' => array('display' => 'popup', 'scope' => 'email')
    );

    /**
     * Возвращает массив опций
     * для формирования redirect_url
     *
     * @return array
     */

    protected function _getRequestOptions()
    {

        $options = parent::_getRequestOptions();
        $options['display'] = 'popup';

        return $options;
    }

    /**
     * Возвращает результат POST-запроса
     * к сервису за токеном
     *
     * Facebook не возвращает JSON,
     * поэтому переопределяем метод
     *
     * @param array $data [code]
     * @return array
     */

    public function getCallback($data)
    {
        if (!isset($data['code'])) {
            return false;
        }

        $client = new Zend_Http_Client($this->_options['requestTokenUrl']);
        $client->setHeaders('Content-Type', 'application/x-www-form-urlencoded')
            ->setHeaders('Accept', 'application/json')
            ->setParameterPost(array(
                'code' => $data['code'],
                'client_id' => $this->_options['key'],
                'client_secret' => $this->_options['secret'],
                'redirect_uri' => $this->_options['callback'],
                'grant_type' => 'authorization_code',
            ));
        if ($response = $client->request(Zend_Http_Client::POST)) {
            parse_str($response->getBody(), $params);
            return $params;
        }

        return false;

    }

    /**
     * Возвращает информацию о пользователе,
     * приведенную к единому формату
     *
     * @param array $userinfo
     * @return array
     */

    public function getMappedUserInfo($userinfo)
    {
        $locale = explode('_', $userinfo['locale']);
        $gender = 0;

        if ($userinfo['gender'] == 'male') {
            $gender = 1;
        } else if ($userinfo['gender'] == 'female') {
            $gender = 2;
        }

        return array(
            'provider' => 'facebook',
            'userid' => $userinfo['id'],
            'username' => $userinfo['name'],
            'gender' => $gender,
            'email' => $userinfo['email'],
            'locale' => strtolower($locale[0]),
            'firstname' => $userinfo['first_name'],
            'lastname' => $userinfo['last_name'],
            'link' => $userinfo['link'],
            'photo' => 'https://graph.facebook.com/' . $userinfo['id'] . '/picture?width=200&height=200',
            'mini_photo' => 'https://graph.facebook.com/' . $userinfo['id'] . '/picture?width=50&height=50'
        );
    }
}
