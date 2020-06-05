<?php
$response = new SapiResponse();
var_dump($response->setCode('500') === $response);
var_dump($response->getCode());
$response->setCode(401);
var_dump($response->getCode());
