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

abstract class ET_SocialLogin_Model_AuthAdapter_Abstract implements ET_SocialLogin_Model_AuthAdapter_Interface
{

    /**
     * Массив опций
     * @var array
     */
    protected $_options = array();

    /**
     * Версия протокола OAuth (
     * Определяется автоматически по наличию в
     * опциях ключа accessTokenUrl)
     *
     * @var int
     */
    protected $_version = 2;

    /**
     *
     * @param array $options
     * @return void
     */

    public function __construct($options = array())
    {

        /**
         * Объединяем опции адаптера с
         * пользовательскими опциями сервиса
         */
        $this->_options = array_merge_recursive($this->_options, $options);

        /**
         * Определяем и устанавливаем версию протокола OAuth
         */
        $this->setVersion();
    }


    /**
     * Устанавливает версию протокола OAuth
     *
     * @param $version int
     * @return int
     */

    public function setVersion($version = 0)
    {

        if (is_int($version) && $version > 0) {
            $this->_version = $version;
        } elseif (isset($this->_options['accessTokenUrl'])) {
            $this->_version = 1;
        } else {
            $this->_version = 2;
        }
        return $this->_version;

    }

    /**
     * Возвращает текущую версию протокола OAuth
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Возвращает массив опций для
     * формирования redirect_url
     *
     * @return array
     */

    protected function _getRequestOptions()
    {
        switch ($this->getVersion()) {
            case 1:
                return array(
                    'siteUrl' => $this->_options['siteUrl'],
                    'authorizeUrl' => $this->_options['authorizeUrl'],
                    'callbackUrl' => $this->_options['callback'],
                    'consumerKey' => $this->_options['key'],
                    'consumerSecret' => $this->_options['secret'],
                    'requestTokenUrl' => $this->_options['requestTokenUrl'],
                    'accessTokenUrl' => $this->_options['accessTokenUrl']
                );
            case 2:
                $options = array(
                    'client_id' => $this->_options['key'],
                    'redirect_uri' => $this->_options['callback'],
                    'response_type' => 'code'
                );
                if (isset($this->_options['request'])) {
                    $options = array_merge_recursive($options, $this->_options['request']);
                }
                return $options;
            default:
                return false;
        }
        return false;
    }


    /**
     * Возвращает урл для перенаправления
     * на страницу авторизации на сервисе
     *
     * @return string
     */

    public function getRequestUrl()
    {
        switch ($this->getVersion()) {
            case 1:
                $consumer = new Zend_Oauth_Consumer($this->_getRequestOptions());
                $token = $consumer->getRequestToken();
                // $session = new Zend_Session_Namespace('OAuth');
                $session = Mage::getSingleton('core/session');
                $session->setToken($token->getTokenSecret());
                //$session->token = $token->getTokenSecret();
                // $session->consumer = $consumer;
                $session->setConsumer($consumer);
                return $consumer->getRedirectUrl();
            case 2:
                return $this->_options['authorizeUrl'] . '?' . http_build_query($this->_getRequestOptions());
            default:
                return false;
        }
        return false;
    }


    /**
     * Возвращает результат POST-запроса к сервису за токеном
     *
     * @param array $data [code] || [oauth_token]
     * @return array
     */

    public function getCallback($data)
    {
        switch ($this->getVersion()) {

            case 1:
                if (!isset($data['oauth_token'])) {
                    return false;
                }
                // $session = new Zend_Session_Namespace('OAuth');
                $request = new Zend_Oauth_Token_Request();
                //   $request->setToken($data['oauth_token'])->setTokenSecret($session->token);
                $request->setToken($data['oauth_token'])
                    ->setTokenSecret(Mage::getSingleton('core/session')->getToken());
                $consumer = new Zend_Oauth_Consumer($this->_getRequestOptions());

                if ($response = $consumer->getAccessToken($data, $request)) {
                    parse_str($response->toString(), $params);
                    return $params;
                }
                return false;
            case 2:
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
                    return Zend_Json::decode($response->getBody());
                }
                return false;
            default:
                return false;
        }
        return false;
    }


    /**
     * Возвращает результат GET-запроса
     * к сервису за информацией о пользователе
     *
     * @param array $callback
     * @return array
     */

    public function getUserInfo($callback)
    {
        $values = isset($this->_options['response']) && is_array($this->_options['response']) ?
            array_intersect_key($callback, array_flip($this->_options['response'])) :
            array();
        $client = new Zend_Http_Client(vsprintf($this->_options['userInfoUrl'], $values));

        if ($response = $client->request(Zend_Http_Client::GET)) {
            return Zend_Json::decode($response->getBody());
        }
        return false;
    }
}
