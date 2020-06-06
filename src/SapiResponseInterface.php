<?php
declare(strict_types=1);

interface SapiResponseInterface
{
    public function setVersion(?string $version) : SapiResponseInterface;

    public function getVersion() : ?string;

    public function setCode(?int $code) : SapiResponseInterface;

    public function getCode() : ?int;

    public function setHeader(string $label, string $value) : SapiResponseInterface;

    public function addHeader(string $label, string $value) : SapiResponseInterface;

    public function unsetHeader(string $label) : SapiResponseInterface;

    public function unsetHeaders() : SapiResponseInterface;

    public function getHeaders() : ?array;

    public function getHeader(string $label) : ?string;

    public function hasHeader(string $label) : bool;

    public function setCookie(
        string $name,
        string $value = "",
        /* int|array */ $expiresOrOptions = null,
        string $path = "",
        string $domain = "",
        bool $secure = false,
        bool $httponly = false
    ) : SapiResponseInterface;

    public function setRawCookie(
        string $name,
        string $value = "",
        /* int|array */ $expiresOrOptions = null,
        string $path = "",
        string $domain = "",
        bool $secure = false,
        bool $httponly = false
    ) : SapiResponseInterface;

    public function unsetCookie(string $name) : SapiResponseInterface;

    public function unsetCookies() : SapiResponseInterface;

    public function getCookies() : ?array;

    public function getCookie(string $name) : ?array;

    public function hasCookie(string $name) : bool;

    public function setHeaderCallbacks(array $callbacks) : SapiResponseInterface;

    public function addHeaderCallback(callable $callback) : SapiResponseInterface;

    public function getHeaderCallbacks() : ?array;

    public function setContent(/* mixed */ $content) : SapiResponseInterface;

    public function getContent() /* : mixed */;
}
