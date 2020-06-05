<?php
$response = new SapiResponse();
$response->setHeader('Foo-Bar', 'baz');
var_dump($response->hasHeader('Foo-Bar'));
var_dump($response->hasHeader('foo-bar'));
var_dump($response->hasHeader('dib-zim'));
