<?php
$response = new SapiResponse();
var_dump($response->setContent('foo') === $response);
var_dump($response->getContent());
