<?php
$response = new SapiResponse();
try {
    $response->addHeader('', 'should-not-show');
} catch (UnexpectedValueException $e) {
    echo $e->getMessage() . PHP_EOL;
}
try {
    $response->addHeader('Should-Not-Show', '');
} catch (UnexpectedValueException $e) {
    echo $e->getMessage() . PHP_EOL;
}
