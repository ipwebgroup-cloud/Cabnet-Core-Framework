<?php
declare(strict_types=1);

namespace Cabnet\Application\Services;

use Cabnet\Application\Crud\CrudEntityDefinition;
use Cabnet\Infrastructure\Repositories\CrudRepositoryContract;

abstract class DefinitionCrudService extends BaseService
{
    public function __construct(
        protected CrudEntityDefinition $definition,
        protected CrudRepositoryContract $repository,
        protected \Validator $validator
    ) {
    }

    /** @return array<string, mixed> */
    public function paginate(string $search = '', int $page = 1, int $perPage = 15, array $filters = []): array
    {
        return $this->repository->findPage(
            searchColumns: $this->definition->searchable(),
            search: $search,
            page: $page,
            perPage: $perPage,
            filters: $filters,
            orderBy: $this->definition->defaultOrder()
        );
    }

    /** @return array<string, mixed>|null */
    public function find(int $id): ?array
    {
        return $this->repository->findById($id);
    }

    public function create(array $input): \ValidationResult
    {
        $result = $this->validator->validate(
            $this->definition->inputPayload($input),
            $this->definition->validationRules()
        );

        if (!$result->valid()) {
            return $result;
        }

        $this->repository->create($result->data());

        return $result;
    }

    public function update(int $id, array $input): \ValidationResult
    {
        $result = $this->validator->validate(
            $this->definition->inputPayload($input),
            $this->definition->validationRules()
        );

        if (!$result->valid()) {
            return $result;
        }

        $this->repository->updateById($id, $result->data());

        return $result;
    }

    public function delete(int $id): bool
    {
        return $this->repository->deleteById($id);
    }
}
