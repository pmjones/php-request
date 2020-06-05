<?php
$response = new SapiResponse();
$response->setContent(function () {
    yield "foo\n";
    yield "bar\n";
    yield "bat\n";
});
(new SapiResponseSender())->send($response);
