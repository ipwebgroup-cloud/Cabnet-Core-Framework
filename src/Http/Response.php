<?php
declare(strict_types=1);

namespace Cabnet\Http;

class Response
{
    protected int $statusCode = 200;
    protected array $headers = [];
    protected string $body = '';

    public function status(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

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
        $this->body = '';
        return $this;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function isRedirect(): bool
    {
        return isset($this->headers['Location']) && $this->statusCode >= 300 && $this->statusCode < 400;
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
