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
 * Интерфейс абстрактного класса адаптеров Phorm_Oauth_Adapter_Abstract
 *
 */

interface ET_SocialLogin_Model_AuthAdapter_Interface
{

    /**
     * Возвращает информацию о пользователе,
     * приведенную к единому формату
     * <pre>
     *    Допустимые поля:
     *        provider - название сервиса (google, yandex и пр.)
     *        userid - id пользователя в сервисе
     *        username - имя пользователя в сервисе
     *
     *        email - основной email пользователя
     *        (если неизвестно, то userid@домен_сервиса)
     *
     *        firstname - имя пользователя
     *        lastname - фамилия пользователя
     *        gender - пол (male,female)
     *        locale - локаль пользователя
     *        link - ссылка на профиль пользователя на сервере сервиса
     *        avatar - адрес аватара на сервере сервиса
     *    Неизвестное значение должно быть установлено в null
     * </pre>
     *
     * @param array $userinfo
     * @return array
     */

    public function getMappedUserInfo($userinfo);


}