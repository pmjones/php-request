<?php
$response = new SapiResponse();
$response->setCookie('cookie1', 'v1&%v2');
var_dump($response->getCookie('cookie1'));
var_dump($response->getCookie('cookie2'));
