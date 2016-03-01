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
 * Адаптер Github
 */

class ET_SocialLogin_Model_AuthAdapter_Github extends ET_SocialLogin_Model_AuthAdapter_Abstract
{

    /**
     * Массив опций
     * @var array
     */
    protected $_options = array(
        'siteUrl' => 'https://github.com',
        'authorizeUrl' => 'https://github.com/login/oauth/authorize',
        'requestTokenUrl' => 'https://github.com/login/oauth/access_token',
        'userInfoUrl' => 'https://api.github.com/user?access_token=%s',
        'response' => array('access_token')
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
        return array(
            'provider' => 'github',
            'userid' => $userinfo['id'],
            'username' => $userinfo['login'],
            'gender' => null,
            'email' => isset($userinfo['email']) ? $userinfo['email'] : '@github.com',
            'locale' => 'ru',
            'firstname' => isset($userinfo['name']) ? $userinfo['name'] : null,
            'lastname' => null,
            'link' => $userinfo['html_url'],
            'avatar' => $userinfo['avatar_url'],
        );
    }
}
