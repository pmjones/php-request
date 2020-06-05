<?php
class ExtResponse extends SapiResponse
{
    public function __construct()
    {
    }
}
$ext = new ExtResponse();
var_dump($ext);
var_dump($ext->getHeaders());
