<?php
declare(strict_types=1);

namespace Cabnet\Infrastructure\Auth;

class DbUserProvider
{
    public function __construct(private \DatabaseManager $db)
    {
    }

    public function findByUsername(string $username): ?array
    {
        return $this->db->first(
            'SELECT * FROM users WHERE username = :username LIMIT 1',
            ['username' => $username]
        );
    }
}
