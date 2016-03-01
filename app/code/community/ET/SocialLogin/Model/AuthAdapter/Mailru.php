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
 * Адаптер Mail.ru (Мой Мир)
 */

class ET_SocialLogin_Model_AuthAdapter_Mailru extends ET_SocialLogin_Model_AuthAdapter_Abstract
{

    /**
     * Массив опций
     * @var array
     */
    protected $_options = array(
        'siteUrl' => 'http://mail.ru',
        'authorizeUrl' => 'https://connect.mail.ru/oauth/authorize',
        'requestTokenUrl' => 'https://connect.mail.ru/oauth/token',
        'userInfoUrl' => '' // Формируется ниже @see self::getUserInfo,
    );

    /**
     * Возвращает информацию о пользователе,
     * приведенную к единому формату
     *
     * @param array $userinfo
     * @return array
     */

    public function getMappedUserInfo($userinfo)
    {
        $userinfo = $userinfo[0];

        return array(
            'provider' => 'mailru',
            'userid' => $userinfo['uid'],
            'username' => $userinfo['nick'],
            'gender' => $userinfo['sex'] == 0 ? 'male' : 'female',
            'email' => $userinfo['email'],
            'locale' => null,
            'firstname' => $userinfo['first_name'],
            'lastname' => $userinfo['last_name'],
            'link' => $userinfo['link'],
            'photo' => $userinfo['pic'],
        );
    }

    /**
     * Возвращает результат GET-запроса к
     * сервису за информацией о пользователе
     *
     * @param array $callback
     * @return array
     */

    public function getUserInfo($callback)
    {
        $params = array(
            'app_id=' . $this->_options['key'],
            'format=' . 'json',
            'method=' . 'users.getInfo',
            'secure=' . 1,
            'session_key=' . $callback['access_token'],
        );
        $params[] = 'sig=' . md5(implode('', $params) . $this->_options['secret']);

        $this->_options['userInfoUrl'] = 'http://www.appsmail.ru/platform/api?' . implode('&', $params);

        return parent::getUserInfo($callback);
    }
}
