Q<?php

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
class ET_SocialLogin_Model_Api_Vk
{
    /**
     * VK application ID.
     * @var int
     */
    private $_appId;

    /**
     * VK application secret key.
     * @var string
     */
    private $_apiSecret;

    /**
     * VK access token.
     * @var string
     */
    private $_accessToken;

    /**
     * Set timeout.
     * @var int
     */
    private $_timeout = 30;

    /**
     * Set connect timeout.
     * @var int
     */
    private $_connecttimeout = 30;

    /**
     * Check SLL certificate.
     * @var bool
     */
    private $_sslVerifypeer = false;

    /**
     * Set library version.
     * @var string
     */
    private $_libVersion = '0.1';

    /**
     * Contains the last HTTP status code returned.
     * @var int
     */
    private $_httpCode;

    /**
     * Contains the last HTTP headers returned.
     * @var mixed See http://www.php.net/manual/en/function.curl-getinfo.php
     */
    private $_httpInfo;

    /**
     * Authorization status.
     * @var bool
     */
    private $_auth = false;

    /**
     * Set base API URLs.
     */
    public function base_authorizeURL()
    {
        return 'http://oauth.vk.com/authorize';
    }

    public function baseAccessTokenURL()
    {
        return 'https://oauth.vk.com/access_token';
    }

    public function getAPI_URL($method)
    {
        return 'https://api.vk.com/method/' . $method;
    }

    /**
     * @param string $_appId
     * @param string $_apiSecret
     * @param string $_accessToken
     * @throws VKException
     */
    public function __construct($_appId, $_apiSecret, $_accessToken = null)
    {
        $this->_appId = $_appId;
        $this->_apiSecret = $_apiSecret;
        $this->_accessToken = $_accessToken;

        if (!is_null($this->_accessToken) && !$this->checkAccessToken()) {
            throw new VKException('Invalid access token.');
        } else {
            $this->_auth = true;
        }
    }

    /**
     * Returns authorization status.
     * @return bool true is auth, false is not auth
     */
    public function is_auth()
    {
        return $this->_auth;
    }

    /**
     * VK API method.
     * @param string $method Contains VK API method.
     * @param array $parameters Contains settings call.
     * @return array
     */
    public function api($method, $parameters = null)
    {
        if (is_null($parameters)) {
            $parameters = array();
        }
        $parameters['api_id'] = $this->_appId;
        $parameters['v'] = $this->_libVersion;
        $parameters['timestamp'] = time();
        $parameters['format'] = 'json';
        $parameters['random'] = rand(0, 10000);

        if (!is_null($this->_accessToken)) {
            $parameters['accessToken'] = $this->_accessToken;
        }

        ksort($parameters);

        $sig = '';
        foreach ($parameters as $key => $value) {
            $sig .= $key . '=' . $value;
        }
        $sig .= $this->_apiSecret;

        $parameters['sig'] = md5($sig);
        $query = $this->createURL($parameters, $this->getAPI_URL($method));

        return $this->http($query);
    }

    /**
     * Get authorize URL.
     * @param string $apiSettings Access rights requested by your app (through comma).
     * @param string $callbackUrl
     * @param bool $testMode
     * @internal param string $callback_url
     * @internal param bool $test_mode
     * @return string
     */
    public function getAuthorizeURL(
        $apiSettings = '',
        $callbackUrl = 'http://oauth.vk.com/blank.html',
        $testMode = false
    ) {
        $parameters = array(
            'client_id' => $this->_appId,
            'scope' => $apiSettings,
            'redirect_uri' => $callbackUrl,
            'response_type' => 'code'
        );

        if ($testMode) {
            $parameters['test_mode'] = '1';
        }

        return $this->createURL($parameters, $this->baseAuthorizeURL());
    }

    /**
     * Get the access token.
     * @param string $code The code to get access token.
     * @param string $callbackUrl
     * @throws VKException
     * @return array(
     *      'accessToken'  => 'the-access-token',
     *      'expires_in'    => '86399', // time life token in seconds
     *      'user_id'       => '12345')
     */
    public function getAccessToken($code, $callbackUrl = 'http://oauth.vk.com/blank.html')
    {
        if (!is_null($this->_accessToken) && $this->_auth) {
            throw new VKException('Already authorized.');
        }

        $parameters = array(
            'client_id' => $this->_appId,
            'client_secret' => $this->_apiSecret,
            'code' => $code,
            'redirect_uri' => $callbackUrl
        );

        $url = $this->createURL($parameters, $this->baseAccessTokenURL());
        $rs = $this->http($url);

        if (isset($rs['error'])) {
            $message = 'HTTP status code: ' . $this->_httpCode . '. ' . $rs['error'];
            if (isset($rs['error_description'])) {
                $message .= ': ' . $rs['error_description'];
            }
            throw new VKException($message);
        } else {
            $this->_auth = true;
            $this->_accessToken = $rs['access_token'];
            return $rs;
        }
    }

    /**
     * Make HTTP request.
     * @param string $url
     * @param string @method Get or Post
     * @param array $postfields If $method post
     * @return array API return
     */
    private function http($url, $method = 'GET', $postfields = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'VK v' . $this->_libVersion);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_connecttimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_sslVerifypeer);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);

            if (!is_null($postfields)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
            }
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        $rs = curl_exec($ch);
        $this->_httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->_httpInfo = curl_getinfo($ch);
        curl_close($ch);

        return json_decode($rs, true);
    }

    /**
     * Create URL from the sended parameters.
     * @param array $parameters Add to base url
     * @param string $url Base url
     * @return string
     */
    private function createURL($parameters, $url)
    {
        $piece = array();
        foreach ($parameters as $key => $value) {
            $piece[] = $key . '=' . rawurlencode($value);
        }

        $url .= '?' . implode('&', $piece);
        return $url;
    }

    /**
     * Check freshness of access token.
     * @return bool true is valid access token else false
     */
    private function checkAccessToken()
    {
        if (is_null($this->_accessToken)) {
            return false;
        }

        $response = $this->api('getUserSettings');
        return isset($response['response']);
    }

}

class VKException extends Exception
{
}
