<?php
$response = new SapiResponse();
$response->setCode(400);
(new SapiResponseSender())->send($response);
var_dump(headers_list());
var_dump(http_response_code());
