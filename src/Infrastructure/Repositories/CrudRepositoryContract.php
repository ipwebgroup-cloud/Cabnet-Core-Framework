<?php
declare(strict_types=1);

namespace Cabnet\Infrastructure\Repositories;

interface CrudRepositoryContract
{
    /** @return array<string, mixed> */
    public function findPage(
        array $searchColumns = [],
        string $search = '',
        int $page = 1,
        int $perPage = 15,
        string $orderBy = 'id DESC'
    ): array;

    /** @return array<string, mixed>|null */
    public function findById(int $id): ?array;

    /** @param array<string, mixed> $data */
    public function create(array $data): bool;

    /** @param array<string, mixed> $data */
    public function updateById(int $id, array $data): bool;

    public function deleteById(int $id): bool;
}
