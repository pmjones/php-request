<?php
$_SERVER = [
    'HTTP_HOST' => 'example.com',
    'REQUEST_METHOD' => 'PUT',
];
class SubSapiRequest extends SapiRequest {
    public $publicTest;
    protected $protectedTest;
    private $privateTest;
    private $magicGetTest;
    public function protectedTest($a) {
        $this->protectedTest = $a;
        return $this->protectedTest;
    }
    public function parentReadOnlyTest() {
        $this->method = 'PATCH';
    }
}
class MagicSapiRequest extends SapiRequest {
    protected $magicTest;
}
class CtorSapiRequest extends SapiRequest {
    public function __construct(array $globals = null) {
        parent::__construct($globals);
        $this->method = 'FOO';
    }
}
$request = new SubSapiRequest($GLOBALS);
var_dump($request->method);
$request->publicTest = 'foo';
var_dump($request->publicTest);
var_dump($request->protectedTest('bar'));
try {
    $request->parentReadOnlyTest();
} catch( Exception $e ) {
    var_dump(get_class($e), $e->getMessage());
}
$request = new MagicSapiRequest($GLOBALS);
$request->magicTest = 'baz';
var_dump($request->magicTest);
$request->magicTestUndef = 'bat';
var_dump($request->magicTestUndef);

// Make sure cloning keeps readonly
$clone = clone $request;
try {
    $clone->method = 'PUT';
    echo 'fail';
} catch( Exception $e ) {
    echo 'ok';
}
echo PHP_EOL;

// constructor overrides of parent properties not allowed
try {
    $request = new CtorSapiRequest($GLOBALS);
} catch (Exception $e) {
    var_dump(get_class($e), $e->getMessage());
}
