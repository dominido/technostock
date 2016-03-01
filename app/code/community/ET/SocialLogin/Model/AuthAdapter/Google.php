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
 * Адаптер Google+
 */

class ET_SocialLogin_Model_AuthAdapter_Google extends ET_SocialLogin_Model_AuthAdapter_Abstract
{

    /**
     * Массив опций
     * @var array
     */
    protected $_options = array(
        'siteUrl' => // Имя домена к которому осуществляются запросы
            'https://accounts.google.com',
        'authorizeUrl' => // Адрес страницы для авторизации
            'https://accounts.google.com/o/oauth2/auth',
        'requestTokenUrl' => // Адрес страницы для получения токена
            'https://accounts.google.com/o/oauth2/token',
        'request' => // Дополнительные параметры запроса при авторизации
            array(
                // 'https://www.googleapis.com/auth/userinfo.email - Oauth 2 denies this scope
                'scope' => 'https://www.googleapis.com/auth/userinfo.profile',
                'display' => 'popup'
            ),
        'userInfoUrl' => //
            'https://www.googleapis.com/oauth2/v1/userinfo?access_token=%s',
        'response' => //
            array('access_token')
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
        $gender = 0;

        if (isset($userinfo['gender'])) {
            if ($userinfo['gender'] == 'male') {
                $gender = 1;
            } else if ($userinfo['gender'] == 'female') {
                $gender = 2;
            }
        }

        return array(
            'provider' => 'google',
            'userid' => $userinfo['id'],
            'username' => $userinfo['name'],
            'gender' => $gender,
            'email' => (isset($userinfo['email'])) ? $userinfo['email'] : '',
            'locale' => (isset($userinfo['locale'])) ? $userinfo['locale'] : '',
            'firstname' => (isset($userinfo['given_name'])) ? $userinfo['given_name'] : '',
            'lastname' => (isset($userinfo['family_name'])) ? $userinfo['family_name'] : '',
            'link' => (isset($userinfo['link'])) ? $userinfo['link'] : '',
            'photo' => $userinfo['picture'] . '?sz=200',
            'mini_photo' => $userinfo['picture'] . '?sz=50',
            'dob' => (isset($userinfo['birthday'])) ? $userinfo['birthday'] : '',
        );

    }

}
