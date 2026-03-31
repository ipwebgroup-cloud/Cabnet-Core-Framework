<?php
declare(strict_types=1);

final class AdminAuthService extends BaseService
{
    public function __construct(
        private DbUserProvider $users,
        private AuthManager $auth
    ) {
    }

    public function attempt(string $username, string $password): bool
    {
        $user = $this->users->findByUsername($username);

        if (!$user || !isset($user['password_hash'])) {
            return false;
        }

        if (!password_verify($password, (string)$user['password_hash'])) {
            return false;
        }

        $this->auth->login([
            'id' => $user['id'] ?? null,
            'name' => $user['name'] ?? $user['username'] ?? 'Administrator',
            'username' => $user['username'] ?? $username,
            'role' => $user['role'] ?? 'admin',
        ]);

        return true;
    }
}
