--TEST--
SapiUpload reflection
--FILE--
<?php
var_dump(new SapiUpload(null, null, null, null, null));
--EXPECT--
object(SapiUpload)#3 (6) {
  ["isUnconstructed":"SapiUpload":private]=>
  bool(false)
  ["name":"SapiUpload":private]=>
  NULL
  ["type":"SapiUpload":private]=>
  NULL
  ["size":"SapiUpload":private]=>
  NULL
  ["tmpName":"SapiUpload":private]=>
  NULL
  ["error":"SapiUpload":private]=>
  NULL
}
