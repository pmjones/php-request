<?php
$response = new SapiResponse();
var_dump($response->setHeader('Foo-Bar', 'baz') === $response);
$response->setHeader('foo-bar', 'dib');
var_dump($response->getHeaders());
$response->unsetHeaders();
var_dump($response->getHeaders());
$response->setHeader('Foo-Bar', 'baz');
$response->setHeader('dib-zim', 'gir');
var_dump($response->getHeaders());
$response->unsetHeader('foo-bar');
$response->unsetHeader('no-such');
var_dump($response->getHeaders());
