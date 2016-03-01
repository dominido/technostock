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


class ET_SocialLogin_Model_AuthAdapter_Twitter extends ET_SocialLogin_Model_AuthAdapter_Abstract
{

    protected $_version = 1;
    /**
     * Массив опций
     * @var array
     */
    protected $_options = array(
        'siteUrl' => 'https://api.twitter.com/oauth',
        'authorizeUrl' => 'https://api.twitter.com/oauth/authorize',
        'requestTokenUrl' => 'https://api.twitter.com/oauth/request_token',
        'accessTokenUrl' => 'https://api.twitter.com/oauth/access_token',
        'userInfoUrl' => 'https://api.twitter.com/1.1/users/show.json',
        'response' => array('user_id')
    );

    /**
     * Возвращает информацию о пользователе,
     * приведенную к единому формату
     *
     *
     * @param array $userinfo
     * @return array
     */

    public function getMappedUserInfo($userinfo)
    {
        $fio = array("", "");
        if (isset($userinfo['name'])) {
            $fio = explode(' ', $userinfo['name']);

        }

        return array(
            'provider' => 'twitter',
            'userid' => $userinfo['id'],
            'username' => $userinfo['screen_name'],
            'gender' => null,
            'email' => $userinfo['id'] . '@twitter.com',
            'locale' => $userinfo['lang'],
            'firstname' => $fio[0],
            'lastname' => isset($fio[1]) ? $fio[1] : "",
            'link' => 'https://twitter.com/' . $userinfo['screen_name'],
            'photo' => $userinfo['profile_image_url'],
        );
    }


    public function getUserInfo($callback)
    {


        $options = array(
            'oauth_consumer_key' => $this->_options['key'],
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_nonce' => rand(1111, 9999),
            'oauth_version' => '1.0',
            'oauth_token' => $callback['oauth_token'],
            'user_id' => $callback["user_id"],
        );

        ksort($options);

        $sig = new Zend_Oauth_Signature_Hmac($this->_options['secret'], $callback['oauth_token_secret'], 'sha1');
        $options['oauth_signature'] = $sig->sign($options, 'GET', $this->_options['userInfoUrl']);

        $client = new Zend_Http_Client($this->_options['userInfoUrl'] . '?' . http_build_query($options));

        if ($response = $client->request(Zend_Http_Client::GET)) {
            return Zend_Json::decode($response->getBody());
        }
        return false;
    }

}

