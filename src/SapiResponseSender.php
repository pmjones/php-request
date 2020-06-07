<?php
declare(strict_types=1);

class SapiResponseSender
{
    public function send(SapiResponseInterface $response) : void
    {
        if (headers_sent($file, $line)) {
            throw new RuntimeException("Headers already sent from {$file} on {$line}.");
        }
        $this->runHeaderCallbacks($response);
        $this->sendStatus($response);
        $this->sendHeaders($response);
        $this->sendCookies($response);
        $this->sendContent($response);
    }

    protected function runHeaderCallbacks(SapiResponseInterface $response) : void
    {
        $callbacks = $response->getHeaderCallbacks() ?? [];
        foreach ($callbacks as $callback) {
            $callback($response);
        }
    }

    protected function sendStatus(SapiResponseInterface $response) : void
    {
        $version = $response->getVersion() ?? '1.1';
        $code = $response->getCode() ?? 200;
        header("HTTP/{$version} {$code}", true, $code);
    }

    protected function sendHeaders(SapiResponseInterface $response) : void
    {
        $headers = $response->getHeaders() ?? [];
        foreach ($headers as $label => $value) {
            header("{$label}: {$value}", false);
        }
    }

    protected function sendCookies(SapiResponseInterface $response) : void
    {
        $cookies = $response->getCookies() ?? [];

        foreach ($cookies as $name => $options) {
            $func = $options['url_encode'] ? 'setcookie' : 'setrawcookie';
            unset($options['url_encode']);

            $value = $options['value'];
            unset($options['value']);

            $func($name, $value, $options);
        }
    }

    protected function sendContent(SapiResponseInterface $response) : void
    {
        $content = $response->getContent();

        if (is_object($content) && is_callable($content)) {
            $content = call_user_func($content, $response);
        }

        if ($content === null || $content === false) {
            return;
        }

        if (is_resource($content)) {
            rewind($content);
            fpassthru($content);
            return;
        }

        if (is_iterable($content)) {
            foreach ($content as $output) {
                echo $output;
            }
            return;
        }

        echo $content;
    }
}
