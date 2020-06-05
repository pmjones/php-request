--TEST--
SapiRequest reflection
--FILE--
<?php
var_dump(new SapiRequest([]));
--EXPECT--
object(SapiRequest)#3 (27) {
  ["isUnconstructed":"SapiRequest":private]=>
  bool(false)
  ["accept":"SapiRequest":private]=>
  array(0) {
  }
  ["acceptCharset":"SapiRequest":private]=>
  array(0) {
  }
  ["acceptEncoding":"SapiRequest":private]=>
  array(0) {
  }
  ["acceptLanguage":"SapiRequest":private]=>
  array(0) {
  }
  ["authDigest":"SapiRequest":private]=>
  array(0) {
  }
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
  array(0) {
  }
  ["files":"SapiRequest":private]=>
  array(0) {
  }
  ["forwarded":"SapiRequest":private]=>
  array(0) {
  }
  ["forwardedFor":"SapiRequest":private]=>
  array(0) {
  }
  ["forwardedHost":"SapiRequest":private]=>
  NULL
  ["forwardedProto":"SapiRequest":private]=>
  NULL
  ["headers":"SapiRequest":private]=>
  array(0) {
  }
  ["input":"SapiRequest":private]=>
  array(0) {
  }
  ["method":"SapiRequest":private]=>
  NULL
  ["query":"SapiRequest":private]=>
  array(0) {
  }
  ["server":"SapiRequest":private]=>
  array(0) {
  }
  ["uploads":"SapiRequest":private]=>
  array(0) {
  }
  ["url":"SapiRequest":private]=>
  array(0) {
  }
}
