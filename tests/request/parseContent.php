<?php
$request = new SapiRequest(['_SERVER' => ['HTTP_CONTENT_TYPE' => null]]);
var_dump($request->contentType);
var_dump($request->contentCharset);

$request = new SapiRequest(['_SERVER' => ['HTTP_CONTENT_TYPE' => '']]);
var_dump($request->contentType);
var_dump($request->contentCharset);

$request = new SapiRequest(['_SERVER' => ['HTTP_CONTENT_TYPE' => '/']]);
var_dump($request->contentType);
var_dump($request->contentCharset);

$request = new SapiRequest(['_SERVER' => ['HTTP_CONTENT_TYPE' => ';']]);
var_dump($request->contentType);
var_dump($request->contentCharset);

$request = new SapiRequest(['_SERVER' => ['HTTP_CONTENT_TYPE' => 'text/']]);
var_dump($request->contentType);
var_dump($request->contentCharset);

$request = new SapiRequest(['_SERVER' => ['HTTP_CONTENT_TYPE' => 'text/plain']]);
var_dump($request->contentType);
var_dump($request->contentCharset);

$request = new SapiRequest(['_SERVER' => ['HTTP_CONTENT_TYPE' => 'text/plain;charset=utf-8']]);
var_dump($request->contentType);
var_dump($request->contentCharset);

$request = new SapiRequest(['_SERVER' => ['HTTP_CONTENT_TYPE' => 'text/plain;foo=bar']]);
var_dump($request->contentType);
var_dump($request->contentCharset);

$request = new SapiRequest(['_SERVER' => ['HTTP_CONTENT_TYPE' => 'text/plain;foo=bar;charset=utf-8;baz=dib']]);
var_dump($request->contentType);
var_dump($request->contentCharset);

$request = new SapiRequest(['_SERVER' => ['HTTP_CONTENT_TYPE' => 'image/x-tiff;baz = "bat"']]);
var_dump($request->contentType);
var_dump($request->contentCharset);

