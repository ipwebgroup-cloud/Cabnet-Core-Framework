<?php
declare(strict_types=1);

final class Migrator
{
    public function __construct(private DatabaseManager $db)
    {
    }

    public function runSqlFile(string $path): void
    {
        if (!is_file($path)) {
            throw new RuntimeException('Migration file not found: ' . $path);
        }

        $sql = file_get_contents($path);
        if (!is_string($sql) || trim($sql) === '') {
            throw new RuntimeException('Migration file is empty: ' . $path);
        }

        $pdo = $this->db->pdo();
        $pdo->exec($sql);
    }
}
