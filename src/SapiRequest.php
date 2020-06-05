<?php
declare(strict_types=1);

/**
 * Read-only server-side request object.
 *
 * @property-read $accept
 * @property-read $acceptCharset
 * @property-read $acceptEncoding
 * @property-read $acceptLanguage
 * @property-read $authDigest
 * @property-read $authPw
 * @property-read $authType
 * @property-read $authUser
 * @property-read $content
 * @property-read $contentCharset
 * @property-read $contentLength
 * @property-read $contentMd5
 * @property-read $contentType
 * @property-read $cookie
 * @property-read $files
 * @property-read $forwarded
 * @property-read $forwardedFor
 * @property-read $forwardedHost
 * @property-read $forwardedProto
 * @property-read $headers
 * @property-read $input
 * @property-read $method
 * @property-read $query
 * @property-read $server
 * @property-read $uploads
 */
class SapiRequest
{
    private /* bool */ $isUnconstructed = true;

    private /* ?array */ $accept;
    private /* ?array */ $acceptCharset;
    private /* ?array */ $acceptEncoding;
    private /* ?array */ $acceptLanguage;
    private /* ?string */ $authDigest;
    private /* ?string */ $authPw;
    private /* ?string */ $authType;
    private /* ?string */ $authUser;
    private /* ?string */ $content;
    private /* ?string */ $contentCharset;
    private /* ?int */ $contentLength;
    private /* ?string */ $contentMd5;
    private /* ?string */ $contentType;
    private /* ?array */ $cookie;
    private /* ?array */ $files;
    private /* ?array */ $forwarded;
    private /* ?array */ $forwardedFor;
    private /* ?string */ $forwardedHost;
    private /* ?string */ $forwardedProto;
    private /* ?array */ $headers;
    private /* ?array */ $input;
    private /* ?string */ $method;
    private /* ?array */ $query;
    private /* ?array */ $server;
    private /* ?array */ $uploads;
    private /* ?array */ $url;

    public function __construct(array $globals = [], ?string $content = null)
    {
        if (! $this->isUnconstructed) {
            $class = get_class($this);
            throw new RuntimeException("{$class}::__construct() called after construction.");
        }

        $this->setGlobal($globals, '_COOKIE', 'cookie');
        $this->setGlobal($globals, '_FILES', 'files');
        $this->setGlobal($globals, '_POST', 'input');
        $this->setGlobal($globals, '_GET', 'query');
        $this->setGlobal($globals, '_SERVER', 'server');

        $this->setHeaders();
        $this->setUploads();

        $this->setAccept();
        $this->setAcceptCharset();
        $this->setAcceptEncoding();
        $this->setAcceptLanguage();
        $this->setAuthDigest();
        $this->setAuthPw();
        $this->setAuthType();
        $this->setAuthUser();
        $this->setContentCharset();
        $this->setContentLength();
        $this->setContentMd5();
        $this->setContentType();
        $this->setForwarded();
        $this->setForwardedFor();
        $this->setForwardedHost();
        $this->setForwardedProto();
        $this->setMethod();
        $this->setUrl();

        $this->content = $content;
        $this->isUnconstructed = false;
    }

    final public function __get(string $key) // : mixed
    {
        if ($key === 'content') {
            return $this->content ?? file_get_contents('php://input');
        }

        if (property_exists($this, $key)) {
            return $this->$key;
        }

        $class = get_class($this);
        throw new RuntimeException("{$class}::\${$key} does not exist.");
    }

    final public function __set(string $key, $val) : void
    {
        $class = get_class($this);

        // problem is that extended classes
        // cannot get their proprties set from the outside,
        // as if they are public
        if (property_exists($this, $key)) {
            throw new RuntimeException("{$class}::\${$key} is read-only.");
        }

        throw new RuntimeException("{$class}::\${$key} does not exist.");
    }

    final public function __isset(string $key) : bool
    {
        if (property_exists($this, $key)) {
            return isset($this->$key);
        }

        return false;
    }

    final public function __unset(string $key) : void
    {
        if (property_exists($this, $key)) {
            $class = get_class($this);
            throw new RuntimeException("{$class}::\${$key} is read-only.");
        }
    }

    private function setGlobal(array $globals, string $key, string $prop) : void
    {
        $global = $globals[$key] ?? [];
        $this->assertImmutable($global, $key);
        $this->$prop = $global;
    }

    protected function setUploads() : void
    {
        $this->uploads = [];

        foreach ($this->files as $key => $spec) {
            $this->uploads[$key] = $this->setUploadsFromSpec($spec);
        }
    }

    protected function setUploadsFromSpec(array $spec) // : array
    {
        if (is_array($spec['tmp_name'])) {
            return $this->setUploadsFromNested($spec);
        }

        return new SapiUpload(
            $spec['name'] ?? null,
            $spec['type'] ?? null,
            $spec['size'] ?? null,
            $spec['tmp_name'] ?? null,
            $spec['error'] ?? null
        );
    }

    protected function setUploadsFromNested(array $nested) // : array
    {
        $uploads = [];
        $keys = array_keys($nested['tmp_name']);
        foreach ($keys as $key) {
            $spec = [
                'error'    => $nested['error'][$key],
                'name'     => $nested['name'][$key],
                'size'     => $nested['size'][$key],
                'tmp_name' => $nested['tmp_name'][$key],
                'type'     => $nested['type'][$key],
            ];
            $uploads[$key] = $this->setUploadsFromSpec($spec);
        }
        return $uploads;
    }

    private function setHeaders() : void
    {
        $this->headers = [];

        // headers prefixed with HTTP_*
        foreach ($this->server ?? [] as $key => $val) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
                $key = str_replace('_', '-', strtolower($key));
                $this->headers[$key] = (string) $val;
            }
        }

        // RFC 3875 headers not prefixed with HTTP_*
        if (isset($this->server['CONTENT_LENGTH'])) {
            $this->headers['content-length'] = (string) $this->server['CONTENT_LENGTH'];
        }

        if (isset($this->server['CONTENT_TYPE'])) {
            $this->headers['content-type'] = (string) $this->server['CONTENT_TYPE'];
        }
    }

    private function setMethod() : void
    {
        $this->method = null;

        if (isset($this->server['REQUEST_METHOD'])) {
            $this->method = strtoupper($this->server['REQUEST_METHOD']);
        }

        if (
            $this->method === 'POST'
            && isset($this->server['HTTP_X_HTTP_METHOD_OVERRIDE'])
        ) {
            $this->method = strtoupper($this->server['HTTP_X_HTTP_METHOD_OVERRIDE']);
        }
    }

    private function setForwardedFor() : void
    {
        $this->forwardedFor = [];

        if (! isset($this->headers['x-forwarded-for'])) {
            return;
        }

        $ips = explode(',', $this->headers['x-forwarded-for']);
        foreach ($ips as $ip) {
            $this->forwardedFor[] = trim($ip);
        }
    }

    private function setForwardedHost() : void
    {
        if (! isset($this->headers['x-forwarded-host'])) {
            return;
        }

        $this->forwardedHost = trim($this->headers['x-forwarded-host']);
    }

    private function setForwardedProto() : void
    {
        if (! isset($this->headers['x-forwarded-proto'])) {
            return;
        }

        $this->forwardedProto = trim($this->headers['x-forwarded-proto']);
    }

    private function setForwarded() : void
    {
        $this->forwarded = [];

        if (! isset($this->headers['forwarded'])) {
            return;
        }

        $forwards = explode(',', $this->headers['forwarded']);
        foreach ($forwards as $forward) {
            $this->forwarded[] = $this->parseForward($forward);
        }
    }

    private function parseForward(string $string) : array
    {
        $forward = [];
        $parts = explode(';', $string);
        foreach ($parts as $part) {
            if (strpos($part, '=') === false) {
                // malformed
                continue;
            }
            list($key, $val) = explode('=', $part);
            $key = strtolower(trim($key));
            $val = trim($val, '\t\n\r\v\"\''); // spaces and quotes
            $forward[$key] = $val;
        }
        return $forward;
    }

    private function setAccept() : void
    {
        $this->accept = [];

        if (! isset($this->headers['accept'])) {
            return;
        }

        $this->accept = $this->parseAccept($this->headers['accept']);
    }

    private function setAcceptCharset() : void
    {
        $this->acceptCharset = [];

        if (! isset($this->headers['accept-charset'])) {
            return;
        }

        $this->acceptCharset = $this->parseAccept($this->headers['accept-charset']);
    }

    private function setAcceptEncoding() : void
    {
        $this->acceptEncoding = [];

        if (! isset($this->headers['accept-encoding'])) {
            return;
        }

        $this->acceptEncoding = $this->parseAccept($this->headers['accept-encoding']);
    }

    private function setAcceptLanguage() : void
    {
        $this->acceptLanguage = [];

        if (! isset($this->headers['accept-language'])) {
            return;
        }

        $langs = $this->parseAccept($this->headers['accept-language']);

        foreach ($langs as $lang) {
            $parts = explode('-', $lang['value']);
            $lang['type'] = array_shift($parts);
            $lang['subtype'] = array_shift($parts);
            $this->acceptLanguage[] = $lang;
        }
    }

    private function parseAccept(string $string) : array
    {
        if (trim($string) === '') {
            return [];
        }

        $buckets = [];

        $values = explode(',', $string);
        foreach ($values as $value) {
            $pairs = explode(';', $value);
            $value = $pairs[0];
            unset($pairs[0]);

            $params = [];
            foreach ($pairs as $pair) {
                $param = [];
                preg_match(
                    '/^(?P<name>.+?)=(?P<quoted>"|\')?(?P<value>.*?)(?:\k<quoted>)?$/',
                    $pair,
                    $param
                );
                $params[$param['name']] = $param['value'];
            }

            $quality = '1.0';
            if (isset($params['q'])) {
                $quality = $params['q'];
                unset($params['q']);
            }

            $buckets[$quality][] = [
                'value' => trim($value),
                'quality' => $quality,
                'params' => $params
            ];
        }

        // reverse-sort the buckets so that q=1 is first and q=0 is last,
        // but the values in the buckets stay in the original order.
        krsort($buckets);

        // flatten the buckets back into the return array
        $array = [];
        foreach ($buckets as $q => $bucket) {
            foreach ($bucket as $spec) {
                $array[] = $spec;
            }
        }

        // done
        return $array;
    }

    private function setContentLength() : void
    {
        if (! isset($this->headers['content-length'])) {
            return;
        }

        $value = trim($this->headers['content-length']);
        $noint = trim($value, '01234567890');
        if ($noint === '') {
            $this->contentLength = (int) $value;
        }
    }

    private function setContentMd5() : void
    {
        if (! isset($this->headers['content-md5'])) {
            return;
        }

        $this->contentMd5 = trim($this->headers['content-md5']);
    }

    private function setContentType() : void
    {
        if (! isset($this->headers['content-type'])) {
            return;
        }

        $parts = explode(';', $this->headers['content-type']);
        if (empty($parts)) {
            return;
        }

        $type = array_shift($parts);
        $regex = "/^[!#$%&'*+.^_`|~0-9A-Za-z-]+\/[!#$%&'*+.^_`|~0-9A-Za-z-]+$/";
        if (preg_match($regex, $type) === 1) {
            $this->contentType = $type;
        }
    }

    private function setContentCharset() : void
    {
        if (! isset($this->headers['content-type'])) {
            return;
        }

        $parts = explode(';', $this->headers['content-type']);
        array_shift($parts);
        if (empty($parts)) {
            return;
        }

        foreach ($parts as $part) {
            $part = str_replace(' ', '', $part);
            if (substr($part, 0, 8) === 'charset=') {
                $this->contentCharset = trim(substr($part, 8));
                return;
            }
        }
    }

    private function setAuthPw() : void
    {
        if (! isset($this->server['PHP_AUTH_PW'])) {
            return;
        }

        $this->authPw = trim($this->server['PHP_AUTH_PW']);
    }

    private function setAuthType() : void
    {
        if (! isset($this->server['PHP_AUTH_TYPE'])) {
            return;
        }

        $this->authType = trim($this->server['PHP_AUTH_TYPE']);
    }

    private function setAuthUser() : void
    {
        if (! isset($this->server['PHP_AUTH_USER'])) {
            return;
        }

        $this->authUser = trim($this->server['PHP_AUTH_USER']);
    }

    private function setAuthDigest() : void
    {
        $this->authDigest = [];

        if (! array_key_exists('PHP_AUTH_DIGEST', $this->server)) {
            return;
        }

        /* modified from https://secure.php.net/manual/en/features.http-auth.php */

        $text = (string) $this->server['PHP_AUTH_DIGEST'];

        $digest = [];
        $needed = [
            'nonce' => true,
            'nc' => true,
            'cnonce' => true,
            'qop' => true,
            'username' => true,
            'uri' => true,
            'response' => true,
        ];

        $regex = '@(\w+)\s*=\s*(?:([\'"])([^\2]+?)\2|([^\s,]+))@';

        preg_match_all(
            $regex,
            $text,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $m) {
            $digest[$m[1]] = $m[3] ? $m[3] : $m[4];
            unset($needed[$m[1]]);
        }

        if (empty($needed)) {
            $this->authDigest = $digest;
            return;
        }

        $this->authDigest = null;
    }

    protected function setUrl() : void
    {
        $this->url = [];

        // scheme
        $scheme = 'http://';
        if (isset($this->server['HTTPS']) && strtolower($this->server['HTTPS']) == 'on') {
            $scheme = 'https://';
        }

        // host
        if (isset($this->server['HTTP_HOST'])) {
            $host = $this->server['HTTP_HOST'];
        } elseif (isset($this->server['SERVER_NAME'])) {
            $host = $this->server['SERVER_NAME'];
        } else {
            $host = '___';
        }

        // port
        preg_match('#\:[0-9]+$#', $host, $matches);
        if ($matches) {
            $host_port = array_pop($matches);
            $host = substr($host, 0, -strlen($host_port));
        }
        $port = isset($this->server['SERVER_PORT'])
            ? ':' . $this->server['SERVER_PORT']
            : '';
        if ($port == '' && ! empty($host_port)) {
            $port = $host_port;
        }

        // all else
        $uri = isset($this->server['REQUEST_URI'])
            ? $this->server['REQUEST_URI']
            : '';

        if ($host == '___' && $port === '' && $uri === '') {
            return;
        }

        $url = $scheme . $host . $port . $uri;
        $base =  [
            'scheme' => null,
            'host' => null,
            'port' => null,
            'user' => null,
            'pass' => null,
            'path' => null,
            'query' => null,
            'fragment' => null,
        ];

        $this->url = array_merge($base, parse_url($url));

        if ($host === '___') {
            $this->url['host'] = null;
        }
    }

    final protected function assertImmutable(
        /* mixed */ $value,
        string $descr
    ) : void
    {
        if (is_null($value) || is_scalar($value)) {
            return;
        }

        if (is_array($value)) {
            foreach ($value as $val) {
                $this->assertImmutable($val, $descr);
            }
            return;
        }

        throw new UnexpectedValueException(
            "All {$descr} values must be null, scalar, or array."
        );
    }
}
