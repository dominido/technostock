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
 * Глобальный класс управления
 * авторизацией через OAuth
 *
 * Компонент возвращает исключительно
 * массив информации о пользователе
 *
 * Особенности по предоставлению доступа к
 * этой информации см. в классе адаптера
 */

class ET_SocialLogin_Model_Oauth
{

    /**
     * Массив опций
     * @var array
     */
    protected $_options = array();

    /**
     * Экземпляр класса адаптера службы
     * @var ET_SocialLogin_Model_AuthAdapter_Abstract
     */
    protected $_adapter;

    /**
     * Конструктор
     *
     * @param mixed $options array | Zend_Config
     * <pre>
     *    Массив опций должен иметь обязательные ключи:
     *        adapter - название класса-адаптера
     *        key - id приложения, которое выдает служба (client_id)
     *        secret - секретный ключ, которое выдает приложение (client_secret)
     *        callback - URL для обработки запросов (redirect_uri)
     * </pre>
     * @return ET_SocialLogin_Model_Oauth
     */

    public function __construct($options)
    {
        $this->setOptions($options);
        $this->setAdapter();
    }

    /**
     * Устанавливает опции
     *
     * @param array $options
     * @throws Mage_Exception
     * @return array
     */

    public function setOptions($options = array())
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (isset($options['adapter'])) {
            if (!class_exists($options['adapter'], true)) {
                throw new Mage_Exception('Couldn\'t load class ' . $options['adapter']);
            }
        } else {
            throw new Mage_Exception('Provider name is empty');
        }

        if (!isset($options['key'])) {
            throw new Mage_Exception('Client ID is empty');
        }

        if (!isset($options['secret'])) {
            throw new Mage_Exception('Secret key is empty');
        }

        if (!isset($options['callback'])) {
            throw new Mage_Exception('Redirect uri is empty');
        }

        $this->_options = $options;

        return $this->getOptions();
    }


    /**
     * Возвращает опции
     *
     * @return array
     */

    public function getOptions()
    {
        return $this->_options;
    }


    /**
     * Устанавливает текущий адаптер
     *
     * @return ET_SocialLogin_Model_AuthAdapter_Abstract
     */

    public function setAdapter()
    {
        $classname = $this->_options['adapter'];
        $this->_adapter = new $classname($this->getOptions());

        return $this->getAdapter();
    }


    /**
     * Возвращает текущий адаптер
     *
     * @return ET_SocialLogin_Model_AuthAdapter_Abstract
     */

    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Возвращает урл для перенаправления
     * на страницу авторизации на сервисе
     *
     * @return string
     */

    public function getRequestUrl()
    {
        return $this->_adapter->getRequestUrl();
    }

    /**
     * Возвращает результат POST-запроса к сервису за токеном
     *
     * @param string $code
     * @return string
     */

    public function getCallback($code)
    {
        return $this->_adapter->getCallback($code);
    }

    /**
     * Возвращает результат GET-запроса к
     * сервису за информацией о пользователе
     *
     * @param string $token
     * @return string
     */

    public function getUserInfo($token)
    {
        return $this->_adapter->getUserInfo($token);
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
        return $this->_adapter->getMappedUserInfo($userinfo);
    }
}
