--TEST--
SapiRequest::__get
--FILE--
<?php
$_SERVER['HTTP_HOST'] = 'localhost';
$request = new SapiRequest($GLOBALS);
var_dump(isset($request->method));
try {
    $request->noSuchProperty;
} catch( Exception $e ) {
    var_dump(get_class($e), $e->getMessage());
}
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
bool(true)
string(16) "RuntimeException"
string(44) "SapiRequest::$noSuchProperty does not exist."

Notice: Indirect modification of overloaded property SapiRequest::$accept has no effect in /Users/pmjones/Code/pmjones/php-request/tests/request/__get.php on line 11

Notice: Indirect modification of overloaded property SapiRequest::$method has no effect in /Users/pmjones/Code/pmjones/php-request/tests/request/__get.php on line 19
