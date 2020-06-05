<?php
$func = function($response) {};
$response = new SapiResponse();
var_dump($response->getHeaderCallbacks() === null);
var_dump(array() === $response->getHeaderCallbacks());

$response->addHeaderCallback($func);
var_dump(count($response->getHeaderCallbacks()));
var_dump(array($func) === $response->getHeaderCallbacks());

$response->addHeaderCallback($func);
var_dump(count($response->getHeaderCallbacks()));
var_dump(array($func, $func) === $response->getHeaderCallbacks());
