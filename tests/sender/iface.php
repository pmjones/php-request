<?php
include "../MySapiResponse.inc";
$response = new MySapiResponse();
$response->setCode(400);
$response->setContent('foo');
$response->setHeader('Foo', 'bar');
$response->setCookie('cookie1', 'v1&%v2');
$response->addHeaderCallback(function($response) {
    $response->addHeader('Baz', 'zim');
});
(new SapiResponseSender())->send($response);
echo "\n";
var_dump(http_response_code());
var_dump(headers_list());
