<?php
$response = new SapiResponse();
$response->setContent(function () {
    yield "foo\n";
    throw new RuntimeException("failure");
    yield "bar\n";
});
try {
    (new SapiResponseSender())->send($response);
} catch (RuntimeException $e) {
    echo $e->getMessage();
}
