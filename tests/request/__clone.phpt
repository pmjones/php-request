--TEST--
SapiRequest::__clone
--FILE--
<?php
$_SERVER['HTTP_HOST'] = 'localhost';

$request = new SapiRequest($GLOBALS);

$_SERVER['HTTP_HOST'] = 'example.com';
$clone = clone $request;
try {
    $clone->method = 'PATCH';
    echo 'fail';
} catch( Exception $e ) {
    echo 'ok';
}
var_dump($request->url['host']);

--EXPECT--
okstring(9) "localhost"
