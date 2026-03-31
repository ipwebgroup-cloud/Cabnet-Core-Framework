<?php
declare(strict_types=1);

final class Connection
{
    private array $config;
    private ?PDO $pdo = null;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function pdo(): PDO
    {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }

        $driver = $this->config['driver'] ?? 'mysql';

        if ($driver !== 'mysql') {
            throw new RuntimeException('Only mysql driver is currently supported.');
        }

        $host = $this->config['host'] ?? 'localhost';
        $port = (int)($this->config['port'] ?? 3306);
        $database = $this->config['database'] ?? '';
        $charset = $this->config['charset'] ?? 'utf8mb4';

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $host,
            $port,
            $database,
            $charset
        );

        $username = $this->config['username'] ?? '';
        $password = $this->config['password'] ?? '';
        $options = $this->config['options'] ?? [];

        $this->pdo = new PDO($dsn, $username, $password, $options);

        return $this->pdo;
    }
}
