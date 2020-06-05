--TEST--
SapiRequest::$content (cli)
--FILE--
<?php
$_SERVER['HTTP_HOST'] = 'localhost';
$request = new SapiRequest($GLOBALS);
var_dump($request->content);
$request = new SapiRequest($GLOBALS, 'foobar');
var_dump($request->content);
--EXPECT--
string(0) ""
string(6) "foobar"
