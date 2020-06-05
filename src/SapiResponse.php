<?php
declare(strict_types=1);

/**
 * Mutable server-side response object.
 */
class SapiResponse
{
    protected /* string */ $version = '1.1';
    protected /* int */ $status = 200;
    protected /* array */ $headers = [];
    protected /* array */ $cookies = [];
    protected /* mixed */ $content;
    protected /* callable */ $headerCallback;

    public function getVersion() : string
    {
        return $this->version;
    }

    public function setVersion(string $version) : void
    {
        $this->version = $version;
    }

    public function getStatus() : int
    {
        return $this->status;
    }

    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    public function getHeader(string $label) : string
    {
        $label = strtolower(trim($label));

        if (isset($this->headers[$label])) {
            return $this->headers[$label];
        }

        return '';
    }

    public function setHeader(string $label, ?string $value) : void
    {
        $label = strtolower(trim($label));
        if ($label === '') {
            return;
        }

        if ($value === null) {
            unset($this->headers[$label]);
        }

        $value = trim($value);
        if ($value === '') {
            unset($this->headers[$label]);
            return;
        }

        $this->headers[$label] = $value;
    }

    public function addHeader(string $label, ?string $value) : void
    {
        $label = strtolower(trim($label));
        if (! $label) {
            return;
        }

        if ($value === null) {
            return;
        }

        $value = trim($value);
        if ($value === '') {
            return;
        }

        if (! isset($this->headers[$label])) {
            $this->headers[$label] = $value;
        } else {
            $this->headers[$label] .= ", {$value}";
        }
    }

    public function getCookies() : array
    {
        return $this->cookies;
    }

    public function setCookie(
        string $name,
        string $value = "",
        int $expire = 0,
        string $path = "",
        string $domain = "",
        bool $secure = false,
        bool $httponly = false
    ) : void
    {
        $this->cookies[$name] = [
            'raw' => false,
            'value' => (string) $value,
            'expire' => (integer) $expire,
            'path' => (string) $path,
            'domain' => (string) $domain,
            'secure' => (boolean) $secure,
            'httponly' => (boolean) $httponly,
        ];
    }

    public function setRawCookie(
        string $name,
        string $value = "",
        int $expire = 0,
        string $path = "",
        string $domain = "",
        bool $secure = false,
        bool $httponly = false
    ) : void
    {
        $this->cookies[$name] = [
            'raw' => true,
            'value' => (string) $value,
            'expire' => (integer) $expire,
            'path' => (string) $path,
            'domain' => (string) $domain,
            'secure' => (boolean) $secure,
            'httponly' => (boolean) $httponly,
        ];
    }

    public function getContent() // : mixed
    {
        return $this->content;
    }

    public function setContent(/* mixed */ $content) : void
    {
        $this->content = $content;
    }

    public function setHeaderCallback(?callable $headerCallback) : void
    {
        $this->headerCallback = $headerCallback;
    }

    public function getHeaderCallback() : ?callable
    {
        return $this->headerCallback;
    }
}
