<?php
declare(strict_types=1);

final class DatabaseManager
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function pdo(): PDO
    {
        return $this->connection->pdo();
    }

    public function select(string $sql, array $params = []): array
    {
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function first(string $sql, array $params = []): ?array
    {
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo()->prepare($sql);
        return $stmt->execute($params);
    }
}
