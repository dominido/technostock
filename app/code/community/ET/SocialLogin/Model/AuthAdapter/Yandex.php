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
 * Адаптер Яндекс (Я.ру)
 */

class ET_SocialLogin_Model_AuthAdapter_Yandex extends ET_SocialLogin_Model_AuthAdapter_Abstract
{

    /**
     * Массив опций
     * @var array
     */
    protected $_options = array(
        'siteUrl' => 'https://oauth.yandex.ru',
        'authorizeUrl' => 'https://oauth.yandex.ru/authorize',
        'requestTokenUrl' => 'https://oauth.yandex.ru/token',
        'userInfoUrl' => 'https://login.yandex.ru/info?format=json&oauth_token=%s',
        'response' => array('access_token'),
        'request' => array('display' => 'popup')
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
        $userinfo['display_name'] = isset($userinfo['display_name']) ? $userinfo['display_name'] : $userinfo['login'];

        if (isset($userinfo['real_name'])) {
            $fio = explode(' ', $userinfo['real_name']);
        }

        $gender = 0;

        if (isset($userinfo['gender'])) {
            if ($userinfo['gender'] == 'male') {
                $gender = 1;
            } else if ($userinfo['gender'] == 'female') {
                $gender = 2;
            }
        }

        return array(
            'provider' => 'yandex',
            'userid' => $userinfo['id'],
            'username' => $userinfo['display_name'],
            'gender' => $gender,
            'email' => (isset($userinfo['default_email'])) ? $userinfo['default_email']
                    : $userinfo['login'] . '@yandex.ru',
            'locale' => null,
            'firstname' =>(isset($fio[1])) ?  $fio[1] : '',
            'lastname' => (isset($fio[0])) ?  $fio[0] : '',
            'link' => 'http://' . $userinfo['display_name'] . '.ya.ru',
            'photo' => null,
        );
    }
}
