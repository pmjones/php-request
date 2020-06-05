<?php
$response = new SapiResponse();
$func = function($response) {};
$response->setHeaderCallbacks(array($func, $func));
var_dump(array($func, $func) === $response->getHeaderCallbacks());
$response->setHeaderCallbacks(array($func));
var_dump(array($func) === $response->getHeaderCallbacks());
