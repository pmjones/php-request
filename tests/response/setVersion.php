<?php
$response = new SapiResponse();
var_dump($response->setVersion('1.1') === $response);
var_dump($response->getVersion());
