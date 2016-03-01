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
 * Адаптер VK.com (ВКонтакте)
 */

class ET_SocialLogin_Model_AuthAdapter_Vk extends ET_SocialLogin_Model_AuthAdapter_Abstract
{
    CONST PROVIDER = 'vk';

    /**
     * Массив опций
     * @var array
     */
    protected $_options = array(
        'siteUrl' => 'http://vk.com',
        'authorizeUrl' => 'https://oauth.vk.com/authorize',
        'requestTokenUrl' => 'https://oauth.vk.com/token',
        'userInfoUrl' => 'https://api.vk.com/method/users.get?fields=uid,first_name,last_name,nickname,screen_name,sex,bdate,photo_200,photo_50&access_token=%s&uid=%s',
        'response' => array('access_token', 'user_id'),
        'request' => array('display' => 'popup')
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
        $userinfo = $userinfo['response'][0];

        if ($userinfo['sex'] == 1) {
            $gender = 2;
        } else if ($userinfo['sex'] == 2) {
            $gender = 1;
        } else {
            $gender = 0;
        }

        return array(
            'provider' => self::PROVIDER,
            'userid' => $userinfo['uid'],
            'username' => !empty($userinfo['nickname']) ? $userinfo['nickname']
                    : $userinfo['first_name'] . ' ' . $userinfo['last_name'],
            'gender' => $gender,
            'email' => null,
            'locale' => null,
            'firstname' => $userinfo['first_name'],
            'lastname' => $userinfo['last_name'],
            'link' => 'http://vk.com/' . $userinfo['screen_name'],
            'photo' => $userinfo['photo_200'],
            'mini_photo' => $userinfo['photo_50'],
            'dob' => date('Y-m-d', strtotime(isset($userinfo['bdate']) ? $userinfo['bdate'] : '0000-00-00')),
        );
    }
}
