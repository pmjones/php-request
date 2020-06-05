--TEST--
SapiRequest::$acceptLanguage
--FILE--
<?php
$request = new SapiRequest([]);
var_dump($request->acceptLanguage);

$_SERVER += [
    'HTTP_HOST' => 'example.com',
    'HTTP_ACCEPT_LANGUAGE' => 'en-US, en-GB, en, *',
];
$request = new SapiRequest($GLOBALS);
var_dump($request->acceptLanguage);
--EXPECTF--
array(0) {
}
array(4) {
  [0]=>
  array(5) {
    ["value"]=>
    string(5) "en-US"
    ["quality"]=>
    string(3) "1.0"
    ["params"]=>
    array(0) {
    }
    ["type"]=>
    string(2) "en"
    ["subtype"]=>
    string(2) "US"
  }
  [1]=>
  array(5) {
    ["value"]=>
    string(5) "en-GB"
    ["quality"]=>
    string(3) "1.0"
    ["params"]=>
    array(0) {
    }
    ["type"]=>
    string(2) "en"
    ["subtype"]=>
    string(2) "GB"
  }
  [2]=>
  array(5) {
    ["value"]=>
    string(2) "en"
    ["quality"]=>
    string(3) "1.0"
    ["params"]=>
    array(0) {
    }
    ["type"]=>
    string(2) "en"
    ["subtype"]=>
    NULL
  }
  [3]=>
  array(5) {
    ["value"]=>
    string(1) "*"
    ["quality"]=>
    string(3) "1.0"
    ["params"]=>
    array(0) {
    }
    ["type"]=>
    string(1) "*"
    ["subtype"]=>
    NULL
  }
}
