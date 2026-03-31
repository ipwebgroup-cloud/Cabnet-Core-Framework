<?php
declare(strict_types=1);

final class Csrf
{
    private string $sessionKey = '_csrf_token';

    public function __construct(private Session $session)
    {
    }

    public function token(): string
    {
        $token = $this->session->get($this->sessionKey);

        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            $this->session->set($this->sessionKey, $token);
        }

        return $token;
    }

    public function validate(?string $token): bool
    {
        $stored = $this->session->get($this->sessionKey);
        return is_string($stored) && is_string($token) && hash_equals($stored, $token);
    }
}
