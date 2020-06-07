--TEST--
SapiRequest::$uploads (real)
--POST_RAW--
Content-Type: multipart/form-data; boundary=---------------------------20896060251896012921717172737
-----------------------------20896060251896012921717172737
Content-Disposition: form-data; name="MAX_FILE_SIZE"

1
-----------------------------20896060251896012921717172737
Content-Disposition: form-data; name="file1"; filename="file1.txt"
Content-Type: text/plain-file1

1
-----------------------------20896060251896012921717172737
Content-Disposition: form-data; name="file2"; filename="file2.txt"
Content-Type: text/plain-file2

22
-----------------------------20896060251896012921717172737
Content-Disposition: form-data; name="file3"; filename="C:\foo\bar/file3.txt"
Content-Type: text/plain-file3;

3
-----------------------------20896060251896012921717172737
Content-Disposition: form-data; name="file4"; filename="file4.txt"

4
-----------------------------20896060251896012921717172737--
--FILE--
<?php
$_SERVER['HTTP_HOST'] = 'example.com';
$request = new SapiRequest($GLOBALS);
var_dump($request->uploads);
--EXPECTF--
array(4) {
  ["file1"]=>
  object(SapiUpload)#2 (6) {
    ["isUnconstructed":"SapiUpload":private]=>
    bool(false)
    ["name":"SapiUpload":private]=>
    string(9) "file1.txt"
    ["type":"SapiUpload":private]=>
    string(16) "text/plain-file1"
    ["size":"SapiUpload":private]=>
    int(1)
    ["tmpName":"SapiUpload":private]=>
    string(%d) "%s"
    ["error":"SapiUpload":private]=>
    int(0)
  }
  ["file2"]=>
  object(SapiUpload)#4 (6) {
    ["isUnconstructed":"SapiUpload":private]=>
    bool(false)
    ["name":"SapiUpload":private]=>
    string(9) "file2.txt"
    ["type":"SapiUpload":private]=>
    string(0) ""
    ["size":"SapiUpload":private]=>
    int(0)
    ["tmpName":"SapiUpload":private]=>
    string(0) ""
    ["error":"SapiUpload":private]=>
    int(2)
  }
  ["file3"]=>
  object(SapiUpload)#5 (6) {
    ["isUnconstructed":"SapiUpload":private]=>
    bool(false)
    ["name":"SapiUpload":private]=>
    string(9) "file3.txt"
    ["type":"SapiUpload":private]=>
    string(16) "text/plain-file3"
    ["size":"SapiUpload":private]=>
    int(1)
    ["tmpName":"SapiUpload":private]=>
    string(%d) "%s"
    ["error":"SapiUpload":private]=>
    int(0)
  }
  ["file4"]=>
  object(SapiUpload)#6 (6) {
    ["isUnconstructed":"SapiUpload":private]=>
    bool(false)
    ["name":"SapiUpload":private]=>
    string(9) "file4.txt"
    ["type":"SapiUpload":private]=>
    string(0) ""
    ["size":"SapiUpload":private]=>
    int(1)
    ["tmpName":"SapiUpload":private]=>
    string(%d) "%s"
    ["error":"SapiUpload":private]=>
    int(0)
  }
}
