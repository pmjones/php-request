<?php
$response = new SapiResponse();
try {
    $response->setHeader('', 'should-not-show');
} catch (UnexpectedValueException $e) {
    echo $e->getMessage() . PHP_EOL;
}
try {
    $response->setHeader('Should-Not-Show', '');
} catch (UnexpectedValueException $e) {
    echo $e->getMessage() . PHP_EOL;
}
