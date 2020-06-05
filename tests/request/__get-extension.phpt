--TEST--
SapiRequest::__get extension indirect modification
--FILE--
<?php
$_SERVER['HTTP_HOST'] = 'localhost';
$request = new SapiRequest($GLOBALS);
try {
    $request->accept[0] = array();
} catch( Exception $e ) {
    var_dump(get_class($e), $e->getMessage());
}
function mut(&$method) {
    $method = 'DELETE';
}
try {
    mut($request->method);
} catch( Exception $e ) {
    var_dump(get_class($e), $e->getMessage());
}
--EXPECT--
Notice: Indirect modification of overloaded property SapiRequest::$accept has no effect in /Users/pmjones/Code/pmjones/php-request/tests/request/__get-extension.php on line 5

Notice: Indirect modification of overloaded property SapiRequest::$method has no effect in /Users/pmjones/Code/pmjones/php-request/tests/request/__get-extension.php on line 13
