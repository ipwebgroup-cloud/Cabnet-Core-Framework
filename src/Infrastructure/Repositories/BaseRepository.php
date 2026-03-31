<?php
declare(strict_types=1);

namespace Cabnet\Infrastructure\Repositories;

abstract class BaseRepository implements CrudRepositoryContract
{
    public function __construct(protected \DatabaseManager $db)
    {
    }

    abstract protected function table(): string;

    abstract public function create(array $data): bool;

    abstract public function updateById(int $id, array $data): bool;

    public function findAll(string $orderBy = 'id DESC'): array
    {
        $sql = 'SELECT * FROM `' . $this->table() . '` ORDER BY ' . $orderBy;
        return $this->db->select($sql);
    }

    public function findPage(
        array $searchColumns = [],
        string $search = '',
        int $page = 1,
        int $perPage = 15,
        string $orderBy = 'id DESC'
    ): array {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        [$whereSql, $params] = $this->buildSearchWhere($searchColumns, $search);

        $countSql = 'SELECT COUNT(*) AS aggregate FROM `' . $this->table() . '`' . $whereSql;
        $countRow = $this->db->first($countSql, $params);
        $total = (int)($countRow['aggregate'] ?? 0);

        $sql = 'SELECT * FROM `' . $this->table() . '`' . $whereSql . ' ORDER BY ' . $orderBy . ' LIMIT ' . (int)$perPage . ' OFFSET ' . (int)$offset;
        $rows = $this->db->select($sql, $params);

        return [
            'rows' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT * FROM `' . $this->table() . '` WHERE id = :id LIMIT 1';
        return $this->db->first($sql, ['id' => $id]);
    }

    public function deleteById(int $id): bool
    {
        $sql = 'DELETE FROM `' . $this->table() . '` WHERE id = :id';
        return $this->db->execute($sql, ['id' => $id]);
    }

    protected function buildSearchWhere(array $columns, string $search): array
    {
        $search = trim($search);

        if ($search === '' || empty($columns)) {
            return ['', []];
        }

        $clauses = [];
        $params = [];

        foreach (array_values($columns) as $index => $column) {
            $param = 'search_' . $index;
            $clauses[] = '`' . $column . '` LIKE :' . $param;
            $params[$param] = '%' . $search . '%';
        }

        return [' WHERE (' . implode(' OR ', $clauses) . ')', $params];
    }
}
