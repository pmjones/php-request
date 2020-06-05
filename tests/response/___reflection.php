<?php
echo preg_replace('/\?(\w+)/', '$1 or NULL', (new ReflectionClass(SapiResponse::CLASS)));
$response = new SapiResponse();
var_dump($response);
var_dump($response->getHeaders());
