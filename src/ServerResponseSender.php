<?php
declare(strict_types=1);

class ServerResponseSender
{
    public function send(ServerResponse $serverResponse) : void
    {
        if (headers_sent($file, $line)) {
            throw new RuntimeException("Headers already sent from {$file} on {$line}.");
        }
        $this->registerHeaderCallback($serverResponse);
        $this->sendStatus($serverResponse);
        $this->sendHeaders($serverResponse);
        $this->sendCookies($serverResponse);
        $this->sendContent($serverResponse);
    }

    protected function registerHeaderCallback(ServerResponse $serverResponse) : void
    {
        $callback = $serverResponse->getHeaderCallback();
        if ($callback !== null) {
            header_register_callback($callback);
        }
    }

    protected function sendStatus(ServerResponse $serverResponse) : void
    {
        $version = $serverResponse->getVersion();
        $status = $serverResponse->getStatus();
        header("HTTP/{$version} {$status}", true, $status);
    }

    protected function sendHeaders(ServerResponse $serverResponse) : void
    {
        foreach ($serverResponse->getHeaders() as $label => $value) {
            header("{$label}: {$value}", false);
        }
    }

    protected function sendCookies(ServerResponse $serverResponse) : void
    {
        foreach ($serverResponse->getCookies() as $name => $args) {
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

    protected function sendContent(ServerResponse $serverResponse) : void
    {
        $content = $serverResponse->getContent();

        if (is_object($content) && is_callable($content)) {
            $content = call_user_func($content, $serverResponse);
        }

        if (is_resource($content)) {
            rewind($content);
            fpassthru($content);
            return;
        }

        echo $content;
    }
}
