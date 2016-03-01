<?php

$url = 'http://pcshop.beta.branderstudio.com/api/v2_soap/?wsdl';
$user = 'pcshop_1c';
$key = '9Lhxnf3DL6Zr3537';

$proxy = new SoapClient($url); // TODO : change url
$sessionId = $proxy->login($user, $key); // TODO : change login and pwd if necessary

$result = $proxy->magentoInfo($sessionId);
var_dump($result);