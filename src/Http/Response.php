<?php
declare(strict_types=1);

namespace Cabnet\Http;

final class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private string $body = '';

    public function html(string $html, int $statusCode = 200): self
    {
        $this->statusCode = $statusCode;
        $this->headers['Content-Type'] = 'text/html; charset=UTF-8';
        $this->body = $html;
        return $this;
    }

    public function json(array $data, int $statusCode = 200): self
    {
        $this->statusCode = $statusCode;
        $this->headers['Content-Type'] = 'application/json; charset=UTF-8';
        $this->body = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: '{}';
        return $this;
    }

    public function redirect(string $location, int $statusCode = 302): self
    {
        $this->statusCode = $statusCode;
        $this->headers['Location'] = $location;
        return $this;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
        echo $this->body;
    }
}
