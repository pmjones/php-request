--TEST--
SapiRequest::__construct without calling parent
--FILE--
<?php
class ExtRequest extends SapiRequest
{
    public function __construct()
    {
    }
}
$ext = new ExtRequest();
var_dump($ext);
var_dump($ext->accept);
--EXPECT--
object(ExtRequest)#3 (27) {
  ["isUnconstructed":"SapiRequest":private]=>
  bool(true)
  ["accept":"SapiRequest":private]=>
  NULL
  ["acceptCharset":"SapiRequest":private]=>
  NULL
  ["acceptEncoding":"SapiRequest":private]=>
  NULL
  ["acceptLanguage":"SapiRequest":private]=>
  NULL
  ["authDigest":"SapiRequest":private]=>
  NULL
  ["authPw":"SapiRequest":private]=>
  NULL
  ["authType":"SapiRequest":private]=>
  NULL
  ["authUser":"SapiRequest":private]=>
  NULL
  ["content":"SapiRequest":private]=>
  NULL
  ["contentCharset":"SapiRequest":private]=>
  NULL
  ["contentLength":"SapiRequest":private]=>
  NULL
  ["contentMd5":"SapiRequest":private]=>
  NULL
  ["contentType":"SapiRequest":private]=>
  NULL
  ["cookie":"SapiRequest":private]=>
  NULL
  ["files":"SapiRequest":private]=>
  NULL
  ["forwarded":"SapiRequest":private]=>
  NULL
  ["forwardedFor":"SapiRequest":private]=>
  NULL
  ["forwardedHost":"SapiRequest":private]=>
  NULL
  ["forwardedProto":"SapiRequest":private]=>
  NULL
  ["headers":"SapiRequest":private]=>
  NULL
  ["input":"SapiRequest":private]=>
  NULL
  ["method":"SapiRequest":private]=>
  NULL
  ["query":"SapiRequest":private]=>
  NULL
  ["server":"SapiRequest":private]=>
  NULL
  ["uploads":"SapiRequest":private]=>
  NULL
  ["url":"SapiRequest":private]=>
  NULL
}
NULL
