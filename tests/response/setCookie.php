<?php
$response = new SapiResponse();
var_dump($response->setCookie('cookie1', 'v1&%v2') === $response);
$response->setRawCookie('cookie2', 'v3&%v4');
$response->setCookie('cookie3', 'value3', 1234567890, "/path", "doma.in", true, true);
$response->setCookie('cookie4', 'value4', [
    'expires' => 1234567890,
    'path' => "/path",
    'domain' => "doma.in",
    'secure' => true,
    'httponly' => true,
    'samesite' => 'lax',
]);
$response->setCookie('empty', '');
var_dump($response->getCookies());
$response->unsetCookies();
var_dump($response->getCookies());
$response->setCookie('foo', 'bar');
$response->setCookie('baz', 'dib');
$response->unsetCookie('foo');
var_dump($response->getCookies());
