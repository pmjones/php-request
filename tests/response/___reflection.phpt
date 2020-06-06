--TEST--
SapiResponse reflection
--FILE--
<?php
$response = new SapiResponse();
var_dump($response);
var_dump($response->getHeaders());
--EXPECT--
object(SapiResponse)#3 (6) {
  ["version":"SapiResponse":private]=>
  NULL
  ["code":"SapiResponse":private]=>
  NULL
  ["headers":"SapiResponse":private]=>
  NULL
  ["cookies":"SapiResponse":private]=>
  NULL
  ["content":"SapiResponse":private]=>
  NULL
  ["callbacks":"SapiResponse":private]=>
  NULL
}
NULL
