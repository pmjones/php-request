<?php

$upload = new SapiUpload('foo', 'text/plain', 123, '/tmp/foo.txt', 0);

$clone = clone $upload;
try {
    $clone->name = 'bar';
    echo "fail\n";
} catch( Exception $e ) {
    echo "ok\n";
}
var_dump($upload->name);

