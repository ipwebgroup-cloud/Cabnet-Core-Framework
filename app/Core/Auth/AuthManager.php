<?php
declare(strict_types=1);

final class AuthManager
{
    public function __construct(
        private Session $session,
        private array $config = []
    ) {
    }

    public function check(): bool
    {
        $sessionKey = $this->config['session_key'] ?? 'cabnet_admin_user';
        return $this->session->has($sessionKey);
    }

    public function user(): mixed
    {
        $sessionKey = $this->config['session_key'] ?? 'cabnet_admin_user';
        return $this->session->get($sessionKey);
    }

    public function login(array $user): void
    {
        $sessionKey = $this->config['session_key'] ?? 'cabnet_admin_user';
        $this->session->regenerate(true);
        $this->session->set($sessionKey, $user);
    }

    public function logout(): void
    {
        $sessionKey = $this->config['session_key'] ?? 'cabnet_admin_user';
        $this->session->forget($sessionKey);
        $this->session->regenerate(true);
    }
}
