<?php
$response = new SapiResponse();
$response->setCookie('cookie1', 'value1', ['nosuchoption' => true]);
try {
    $response->setCookie('cookie2', 'value2', ['expires' => 1234567890], '/path');
} catch (BadMethodCallException $e) {
    echo $e->getMessage() . PHP_EOL;
}
