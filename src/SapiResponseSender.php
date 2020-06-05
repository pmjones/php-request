<?php
declare(strict_types=1);

class SapiResponseSender
{
    public function send(SapiResponse $SapiResponse) : void
    {
        if (headers_sent($file, $line)) {
            throw new RuntimeException("Headers already sent from {$file} on {$line}.");
        }
        $this->registerHeaderCallback($SapiResponse);
        $this->sendStatus($SapiResponse);
        $this->sendHeaders($SapiResponse);
        $this->sendCookies($SapiResponse);
        $this->sendContent($SapiResponse);
    }

    protected function registerHeaderCallback(SapiResponse $SapiResponse) : void
    {
        $callback = $SapiResponse->getHeaderCallback();
        if ($callback !== null) {
            header_register_callback($callback);
        }
    }

    protected function sendStatus(SapiResponse $SapiResponse) : void
    {
        $version = $SapiResponse->getVersion();
        $status = $SapiResponse->getStatus();
        header("HTTP/{$version} {$status}", true, $status);
    }

    protected function sendHeaders(SapiResponse $SapiResponse) : void
    {
        foreach ($SapiResponse->getHeaders() as $label => $value) {
            header("{$label}: {$value}", false);
        }
    }

    protected function sendCookies(SapiResponse $SapiResponse) : void
    {
        foreach ($SapiResponse->getCookies() as $name => $args) {
            if ($args['raw']) {
                setrawcookie(
                    $name,
                    $args['value'],
                    $args['expire'],
                    $args['path'],
                    $args['domain'],
                    $args['secure'],
                    $args['httponly']
                );
            } else {
                setcookie(
                    $name,
                    $args['value'],
                    $args['expire'],
                    $args['path'],
                    $args['domain'],
                    $args['secure'],
                    $args['httponly']
                );
            }
        }
    }

    protected function sendContent(SapiResponse $SapiResponse) : void
    {
        $content = $SapiResponse->getContent();

        if (is_object($content) && is_callable($content)) {
            $content = call_user_func($content, $SapiResponse);
        }

        if (is_resource($content)) {
            rewind($content);
            fpassthru($content);
            return;
        }

        echo $content;
    }
}
