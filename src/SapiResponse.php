<?php
declare(strict_types=1);

/**
 * Mutable SAPI response object.
 */
class SapiResponse implements SapiResponseInterface
{
    private /* ?string */ $version;
    private /* ?int */ $code;
    private /* ?array */ $headers;
    private /* ?array */ $cookies;
    private /* ?mixed */ $content;
    private /* ?array */ $callbacks;

    final public function setVersion(?string $version) : SapiResponseInterface
    {
        $this->version = $version;
        return $this;
    }

    final public function getVersion() : ?string
    {
        return $this->version;
    }

    final public function setCode(?int $code) : SapiResponseInterface
    {
        $this->code = $code;
        return $this;
    }

    final public function getCode() : ?int
    {
        return $this->code;
    }

    final public function setHeader(string $label, string $value) : SapiResponseInterface
    {
        $label = strtolower(trim($label));
        if ($label === '') {
            throw new UnexpectedValueException('Header label cannot be blank');
        }

        if ($value === '') {
            throw new UnexpectedValueException('Header value cannot be blank');
        }

        $this->headers[$label] = $value;
        return $this;
    }

    final public function addHeader(string $label, string $value) : SapiResponseInterface
    {
        $label = strtolower(trim($label));
        if ($label === '') {
            throw new UnexpectedValueException('Header label cannot be blank');
        }

        if ($value === '') {
            throw new UnexpectedValueException('Header value cannot be blank');
        }

        if (! isset($this->headers[$label])) {
            $this->headers[$label] = $value;
        } else {
            $this->headers[$label] .= ", {$value}";
        }

        return $this;
    }

    final public function unsetHeader(string $label) : SapiResponseInterface
    {
        $label = strtolower(trim($label));
        unset($this->headers[$label]);
        return $this;
    }

    final public function unsetHeaders() : SapiResponseInterface
    {
        $this->headers = null;
        return $this;
    }

    final public function getHeaders() : ?array
    {
        return $this->headers;
    }

    final public function getHeader(string $label) : ?string
    {
        $label = strtolower(trim($label));
        return $this->headers[$label] ?? null;
    }

    final public function hasHeader(string $label) : bool
    {
        $label = strtolower(trim($label));
        return isset($this->headers[$label]);
    }

    final public function setCookie(
        string $name,
        string $value = "",
        /* int|array */ $expiresOrOptions = null,
        string $path = "",
        string $domain = "",
        bool $secure = false,
        bool $httponly = false
    ) : SapiResponseInterface
    {
        return $this->parseCookie(
            $name,
            $value,
            $expiresOrOptions,
            func_num_args(),
            $path,
            $domain,
            $secure,
            $httponly,
            true
        );
    }

    final public function setRawCookie(
        string $name,
        string $value = "",
        /* int|array */ $expiresOrOptions = null,
        string $path = "",
        string $domain = "",
        bool $secure = false,
        bool $httponly = false
    ) : SapiResponseInterface
    {
        return $this->parseCookie(
            $name,
            $value,
            $expiresOrOptions,
            func_num_args(),
            $path,
            $domain,
            $secure,
            $httponly,
            false
        );
    }

    private function parseCookie(
        string $name,
        string $value,
        /* int|array */ $expiresOrOptions,
        int $numArgs,
        string $path,
        string $domain,
        bool $secure,
        bool $httponly,
        bool $urlEncode
    ) : SapiResponseInterface
    {
        $expires = 0;
        $samesite = '';

        if ($expiresOrOptions !== null) {
            if (is_array($expiresOrOptions)) {
                if ($numArgs > 3) {
                    trigger_error("Cannot pass arguments after the options array", E_USER_WARNING);
                    return $this;
                }
                $expires = 0;
                $this->parseCookieOptions(
                    $expiresOrOptions,
                    $expires,
                    $path,
                    $domain,
                    $secure,
                    $httponly,
                    $samesite
                );
            } else {
                $expires = (int) $expiresOrOptions;
            }
        }

        if ($this->cookies === null) {
            $this->cookies = [];
        }

        $this->cookies[$name] = [
            'value' => $value,
            'expires' => $expires,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite,
            'url_encode' => $urlEncode
        ];

        return $this;
    }

    private function parseCookieOptions(
        array $options,
        int &$expires,
        string &$path,
        string &$domain,
        bool &$secure,
        bool &$httponly,
        string &$samesite
    ) : void
    {
        $found = 0;
        foreach ($options as $key => $value) {
            if (is_string($key)) {
                if ($key == "expires") {
                    $expires = (int) $value;
                    $found ++;
                } elseif ($key == "path") {
                    $path = (string) $value;
                    $found ++;
                } elseif ($key == "domain") {
                    $domain = (string) $value;
                    $found ++;
                } elseif ($key == "secure") {
                    $secure = (bool) $value;
                    $found ++;
                } elseif ($key == "httponly") {
                    $httponly = (bool) $value;
                    $found ++;
                } elseif ($key == "samesite") {
                    $samesite = (string) $value;
                    $found ++;
                } else {
                    trigger_error("SapiResponse::setCookie(): Unrecognized key '{$key}' found in the options array", E_USER_WARNING);
                }
            } else {
                trigger_error("SapiResponse::setCookie(): Numeric key found in the options array", E_USER_WARNING);
            }
        }

        if ($found == 0 && count($options) > 0) {
            trigger_error("SapiResponse::setCookie(): No valid options were found in the given array", E_USER_WARNING);
        }
    }

    final public function unsetCookie(string $name) : SapiResponseInterface
    {
        unset($this->cookies[$name]);
        return $this;
    }

    final public function unsetCookies() : SapiResponseInterface
    {
        $this->cookies = null;
        return $this;
    }

    final public function getCookies() : ?array
    {
        return $this->cookies;
    }

    final public function getCookie(string $name) : ?array
    {
        return $this->cookies[$name] ?? null;
    }

    final public function hasCookie(string $name) : bool
    {
        return isset($this->cookies[$name]);
    }

    final public function setHeaderCallbacks(array $headerCallbacks) : SapiResponseInterface
    {
        $this->callbacks = [];
        foreach ($headerCallbacks as $headerCallback) {
            $this->addHeaderCallback($headerCallback);
        }
        return $this;
    }

    final public function addHeaderCallback(callable $headerCallback) : SapiResponseInterface
    {
        if (! is_array($this->callbacks)) {
            $this->callbacks = [];
        }

        $this->callbacks[] = $headerCallback;
        return $this;
    }

    final public function getHeaderCallbacks() : ?array
    {
        return $this->callbacks;
    }

    final public function setContent(/* mixed */ $content) : SapiResponseInterface
    {
        $this->content = $content;
        return $this;
    }

    final public function getContent() // : mixed
    {
        return $this->content;
    }
}
