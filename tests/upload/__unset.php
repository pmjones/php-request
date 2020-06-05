<?php
$upload = new SapiUpload('foo', 'text/plain', 123, '/tmp/foo.txt', 0);
unset($upload->no_such_prop);
try {
    unset($upload->name);
} catch( Exception $e ) {
    var_dump(get_class($e), $e->getMessage());
}
