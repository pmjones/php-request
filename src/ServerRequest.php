<?php
declare(strict_types=1);

/**
 * Read-only server-side request object.
 *
 * Has to be extensible so that frameworks can add their own stuff,
 * and leave it usable by other systems that only need the base ServerRequest.
 * Keep everything here private and final, so it cannot be overridden? Then
 * it's guaranteed to stay the same.
 *
 * Cannot final the properties, which means they can be replaced by extended
 * classes which could mess with them. Unless the read-only nature in PHP itself
 * really sticks? ALternatively, since __get() etc. are final here, they will
 * read *these* properties preferentially.
 *
 * How to make $content lazy-loadable?
 *
 * @property-read $acceptContentCharset
 * @property-read $acceptContentEncoding
 * @property-read $acceptContentLanguage
 * @property-read $acceptContentType
 * @property-read $content
 * @property-read $contentCharset
 * @property-read $contentLength
 * @property-read $contentMd5
 * @property-read $contentType
 * @property-read $cookie
 * @property-read $env
 * @property-read $files
 * @property-read $forwarded
 * @property-read $forwardedFor
 * @property-read $forwardedHost
 * @property-read $forwardedProto
 * @property-read $get
 * @property-read $headers
 * @property-read $method
 * @property-read $phpAuthDigest
 * @property-read $phpAuthPw
 * @property-read $phpAuthType
 * @property-read $phpAuthUser
 * @property-read $post
 * @property-read $requestedWith
 * @property-read $server
 * @property-read $uploads
 */
class ServerRequest
{
    private /* bool */ $initialized = false;

    private /* array */ $acceptContentType = [];
    private /* array */ $acceptContentCharset = [];
    private /* array */ $acceptContentEncoding = [];
    private /* array */ $acceptContentLanguage = [];
    private /* string */ $phpAuthDigest = '';
    private /* string */ $phpAuthPw = '';
    private /* string */ $phpAuthType = '';
    private /* string */ $phpAuthUser = '';
    private /* string */ $content = '';
    private /* string */ $contentCharset = '';
    private /* string */ $contentLength = '';
    private /* string */ $contentMd5 = '';
    private /* string */ $contentType = '';
    private /* array */ $cookie = [];
    private /* array */ $env = [];
    private /* array */ $files = [];
    private /* array */ $forwarded = [];
    private /* array */ $forwardedFor = [];
    private /* string */ $forwardedHost = '';
    private /* string */ $forwardedProto = '';
    private /* array */ $get = [];
    private /* array */ $headers = [];
    private /* string */ $method = '';
    private /* array */ $post = [];
    private /* array */ $server = [];
    private /* array */ $uploads = [];

    public function __construct(array $globals = [])
    {
        if ($this->initialized) {
            $class = get_class($this);
            throw new RuntimeException("{$class}::__construct() called after construction.");
        }

        $this->cookie = $this->parseGlobal($globals, '_COOKIE');
        $this->env = $this->parseGlobal($globals, '_ENV');
        $this->files = $this->parseGlobal($globals, '_FILES');
        $this->get = $this->parseGlobal($globals, '_GET');
        $this->post = $this->parseGlobal($globals, '_POST');
        $this->server = $this->parseGlobal($globals, '_SERVER');
        $this->uploads = $this->parseUploads();
        $this->headers = $this->getHeaders();

        $this->acceptContentCharset = $this->parseAcceptContentCharset();
        $this->acceptContentEncoding = $this->parseAcceptContentEncoding();
        $this->acceptContentLanguage = $this->parseAcceptContentLanguage();
        $this->acceptContentType = $this->parseAcceptContentType();
        $this->contentCharset = $this->parseContentCharset();
        $this->contentLength = $this->parseCntentLength();
        $this->contentMd5 = $this->parseContentMd5();
        $this->contentType = $this->parseContentType();
        $this->forwarded = $this->parseForwarded();
        $this->forwardedFor = $this->parseForwardedFor();
        $this->forwardedHost = $this->parseForwardedHost();
        $this->forwardedProto = $this->parseForwardedProto();
        $this->method = $this->parseMethod();
        $this->phpAuthDigest = $this->parsePhpAuthDigest();
        $this->phpAuthPw = $this->parsePhpAuthPw();
        $this->phpAuthType = $this->parsePhpAuthType();
        $this->phpAuthUser = $this->parsePhpAuthUser();
        $this->requestedWith = $this->parseRequestedWith();

        $this->initialized = true;
    }

    final public function __get(string $key) // : mixed
    {
        if ($key === 'content' && $this->content === null) {
            $this->content = $this->parseContent();
        }

        if (property_exists($this, $key) && $key{0} !== '_') {
            return $this->$key;
        }

        $class = get_class($this);
        throw new RuntimeException("{$class}::\${$key} does not exist.");
    }

    final public function __set(string $key, $val) : void
    {
        $class = get_class($this);
        throw new RuntimeException("{$class}::\${$key} is read-only.");
    }

    final public function __isset($key) : bool
    {
        if (property_exists($this, $key) && $key{0} !== '_') {
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

    private function parseGlobal(array $globals, string $key) : array
    {
        $global = $globals[$key] ?? [];
        $this->assertImmutable($global, $key);
        return $global;
    }

    private function parseUploads() : array
    {
        $uploads = [];
        foreach ($this->files as $key => $spec) {
            $uploads[$key] = $this->parseUploadsFromSpec($spec);
        }
        return $uploads;
    }

    private function parseUploadsFromSpec(array $spec) : array
    {
        if (is_array($spec['tmp_name'] ?? null)) {
            return $this->parseUploadsFromNested($spec);
        }

        return $spec;
    }

    private function parseUploadsFromNested(array $nested) : array
    {
        $uploads = [];
        $keys = array_keys($nested['tmp_name']);
        foreach ($keys as $key) {
            $spec = [
                'error'    => $nested['error'][$key] ?? null,
                'name'     => $nested['name'][$key] ?? null,
                'size'     => $nested['size'][$key] ?? null,
                'tmp_name' => $nested['tmp_name'][$key] ?? null,
                'type'     => $nested['type'][$key] ?? null,
            ];
            $uploads[$key] = $this->setUploadsFromSpec($spec);
        }
        return $uploads;
    }

    private function parseHeaders() : array
    {
        $headers = [];

        // headers prefixed with HTTP_*
        foreach ($this->server as $key => $val) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
                $key = str_replace('_', '-', strtolower($key));
                $headers[$key] = (string) $val;
            }
        }

        // RFC 3875 headers not prefixed with HTTP_*
        if (isset($this->server['CONTENT_LENGTH'])) {
            $headers['content-length'] = (string) $this->server['CONTENT_LENGTH'];
        }

        if (isset($this->server['CONTENT_TYPE'])) {
            $headers['content-type'] = (string) $this->server['CONTENT_TYPE'];
        }

        return $headers;
    }

    private function parseMethod() : string
    {
        $method = null;

        if (isset($this->server['REQUEST_METHOD'])) {
            $method = strtoupper($this->server['REQUEST_METHOD']);
        }

        if (
            $method === 'POST'
            && isset($this->server['HTTP_X_HTTP_METHOD_OVERRIDE'])
        ) {
            $method = strtoupper($this->server['HTTP_X_HTTP_METHOD_OVERRIDE']);
        }

        return $method;
    }

    private function parseForwardedFor() : array
    {
        if (! isset($this->headers['x-forwarded-for'])) {
            return [];
        }

        $forwardedFor = [];
        $ips = explode(',', $this->headers['x-forwarded-for']);
        foreach ($ips as $ip) {
            $forwardedFor[] = trim($ip);
        }

        return $forwardedFor;
    }

    private function parseForwardedHost() : string
    {
        return trim($this->headers['x-forwarded-host'] ?? '');
    }

    private function parseForwardedProto() : string
    {
        return trim($this->headers['x-forwarded-proto'] ?? '');
    }

    private function parseForwarded() : array
    {
        if (! isset($this->headers['forwarded'])) {
            return [];
        }

        $forwarded = [];
        $forwards = explode(',', $this->headers['forwarded']);
        foreach ($forwards as $forward) {
            $forwarded[] = $this->parseForward($forward);
        }

        return $forwarded;
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

    private function parseAcceptContentType() : array
    {
        return $this->parseAccept($this->headers['accept'] ?? null);
    }

    private function parseAcceptContentCharset() : array
    {
        return $this->parseAccept($this->headers['accept-charset'] ?? null);
    }

    private function parseAcceptContentEncoding() : array
    {
        return $this->parseAccept($this->headers['accept-encoding'] ?? null);
    }

    private function parseAcceptContentLanguage() : array
    {
        if (! isset($this->headers['accept-language'])) {
            return [];
        }

        $acceptLanguage = [];
        $langs = $this->parseAccept($this->headers['accept-language']);
        foreach ($langs as $lang) {
            $parts = explode('-', $lang['value']);
            $lang['type'] = array_shift($parts);
            $lang['subtype'] = array_shift($parts);
            $acceptLanguage[] = $lang;
        }
        return $acceptLanguage;
    }

    private function parseAccept(?string $string) : array
    {
        if ($string === null) {
            return [];
        }

        $buckets = [];

        $values = explode(',', $string);
        foreach ($values as $index => $value) {
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
                'index' => $index,
                'params' => $params
            ];
        }

        // reverse-sort the buckets so that q=1 is first and q=0 is last,
        // but the values in the buckets stay in the original order.
        krsort($buckets);

        // flatten the buckets back into the return array
        $accept = [];
        foreach ($buckets as $q => $bucket) {
            foreach ($bucket as $spec) {
                $accept[] = $spec;
            }
        }

        // done
        return $accept;
    }

    private function parseContent() : string
    {
        return file_get_contents('php://input');
    }

    private function parseContentLength() : string
    {
        return trim($this->headers['content-length']);
    }

    private function parseContentMd5() : string
    {
        return trim($this->headers['content-md5']);
    }

    private function parseContentType() : string
    {
        if (! isset($this->headers['content-type'])) {
            return '';
        }

        $parts = explode(';', $this->headers['content-type']);
        return trim(array_shift($parts));
    }

    private function parseContentCharset() : string
    {
        if (! isset($this->headers['content-type'])) {
            return '';
        }

        $parts = explode(';', $this->headers['content-type']);
        array_shift($parts);
        if (empty($parts)) {
            return '';
        }

        foreach ($parts as $part) {
            $part = str_replace(' ', '', $part);
            if (substr($part, 0, 8) === 'charset=') {
                return trim(substr($part, 8));
            }
        }

        return '';
    }

    private function parsePhpAuthPw() : string
    {
        return trim($this->server['PHP_AUTH_PW'] ?? '');
    }

    private function parsePhpAuthType() : string
    {
        return trim($this->server['PHP_AUTH_TYPE'] ?? '');
    }

    private function parsePhpAuthUser() : string
    {
        return trim($this->server['PHP_AUTH_USER'] ?? '');
    }

    private function parsePhpAuthDigest() : array
    {
        if (! isset($this->server['PHP_AUTH_DIGEST'])) {
            return [];
        }

        /* modified from https://secure.php.net/manual/en/features.http-auth.php */

        $text = $this->server['PHP_AUTH_DIGEST'];

        $digest = [];
        $missing = [
            'nonce' => true,
            'nc' => true,
            'cnonce' => true,
            'qop' => true,
            'username' => true,
            'uri' => true,
            'response' => true,
        ];
        $keys = implode('|', array_keys($missing));

        preg_match_all(
            '@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@',
            $text,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $m) {
            $digest[$m[1]] = $m[3] ? $m[3] : $m[4];
            unset($missing[$m[1]]);
        }

        if (empty($missing)) {
            return $digest;
        }

        return [];
    }

    private function parseRequestedWith() : string
    {
        return trim($this->server['HTTP_X_REQUESTED_WITH'] ?? '');
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
