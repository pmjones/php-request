<?php
$response = new SapiResponse();
$response->addHeaderCallback(function($response) {
    $response->setHeader('Foo', 'bar');
    $response->setHeader('Baz', 'dib');
    $response->addHeader('Baz', 'zim');
});
(new SapiResponseSender())->send($response);
var_dump(headers_list());
// it appears EXPECTHEADERS can't handle duplicate headers
