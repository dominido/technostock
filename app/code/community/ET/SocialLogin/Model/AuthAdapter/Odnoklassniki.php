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
 * Адаптер Одноклассники.ру
 */

class ET_SocialLogin_Model_AuthAdapter_Odnoklassniki extends ET_SocialLogin_Model_AuthAdapter_Abstract
{

    /**
     * Массив опций
     * @var array
     */
    protected $_options = array(
        'siteUrl' => 'http://odnokassniki.ru',
        'authorizeUrl' => 'http://www.odnoklassniki.ru/oauth/authorize',
        'requestTokenUrl' => 'http://api.odnoklassniki.ru/oauth/token.do',
        'userInfoUrl' => '', // Формируется ниже @see self::getUserInfo
        'request' => array(
            'scope' => 'VALUABLE ACCESS'
        )
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

        if ($userinfo['gender'] == 'male') {
            $gender = 1;
        } else if ($userinfo['gender'] == 'female') {
            $gender = 2;
        }

        return array(
            'provider' => 'odnoklassniki',
            'userid' => $userinfo['uid'],
            'username' => $userinfo['name'],
            'gender' => $gender,
            'email' => $userinfo['uid'] . '@odnoklassniki.ru',
            'locale' => $userinfo['locale'],
            'firstname' => $userinfo['first_name'],
            'lastname' => $userinfo['last_name'],
            'link' => 'http://www.odnoklassniki.ru/profile/' . $userinfo['uid'],
            'photo' => $userinfo['pic_2'],
            'mini_photo' => $userinfo['pic_1'],
            'dob' => $userinfo['birthday'],
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
            'application_key=' . $this->_options['appkey'],
            'format=' . 'json',
            'method=' . 'users.getCurrentUser',
        );
        $params[] = 'sig=' . md5(implode('', $params) . md5($callback['access_token'] . $this->_options['secret']));
        $params[] = 'access_token=' . $callback['access_token'];

        $this->_options['userInfoUrl'] = 'http://api.odnoklassniki.ru/fb.do?' . implode('&', $params);

        return parent::getUserInfo($callback);
    }
}
