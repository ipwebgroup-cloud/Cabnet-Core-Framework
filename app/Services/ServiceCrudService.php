<?php
declare(strict_types=1);

final class ServiceCrudService extends BaseService
{
    public function __construct(
        private ServiceRepository $repository,
        private Validator $validator
    ) {
    }

    public function all(): array
    {
        return $this->repository->findAll('id DESC');
    }

    public function paginate(string $search = '', int $page = 1, int $perPage = 15): array
    {
        return $this->repository->findPage(
            searchColumns: ServiceEntityDefinition::make()->searchable(),
            search: $search,
            page: $page,
            perPage: $perPage,
            orderBy: ServiceEntityDefinition::make()->defaultOrder()
        );
    }

    public function find(int $id): ?array
    {
        return $this->repository->findById($id);
    }

    public function create(array $input): ValidationResult
    {
        $result = $this->validator->validate($input, [
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'slug' => ['required', 'slug', 'min:2', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
            'summary' => ['string', 'max:1000'],
        ]);

        if (!$result->valid()) {
            return $result;
        }

        $this->repository->create($result->data());

        return $result;
    }

    public function update(int $id, array $input): ValidationResult
    {
        $result = $this->validator->validate($input, [
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'slug' => ['required', 'slug', 'min:2', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
            'summary' => ['string', 'max:1000'],
        ]);

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
