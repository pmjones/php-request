<?php
$upload = new SapiUpload('foo', 'text/plain', 123, '/tmp/foo.txt', 0);
try {
    $upload->name = 'bar';
} catch( Exception $e ) {
    var_dump(get_class($e), $e->getMessage());
}
try {
    $upload->noSuchProperty = 'foo';
} catch( Exception $e ) {
    var_dump(get_class($e), $e->getMessage());
}
