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
var_dump(headers_list());
