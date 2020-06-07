--TEST--
SapiResponseSender::send (extended without constructor)
--CGI--
--INI--
expose_php=0
--FILE--
<?php
class ExtResponse extends SapiResponse
{
}
$ext = new ExtResponse();
$ext->setHeader('foo', 'bar');
$ext->setCookie('baz', 'dib');
$ext->setContent('content');
(new SapiResponseSender())->send($ext);
echo "\n";
--EXPECTHEADERS--
foo: bar
Set-Cookie: baz=dib
Content-type: text/html; charset=UTF-8
--EXPECT--
content
