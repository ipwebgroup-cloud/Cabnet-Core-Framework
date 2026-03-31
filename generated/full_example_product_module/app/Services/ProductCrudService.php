<?php
declare(strict_types=1);

final class ProductCrudService extends BaseService
{
    public function __construct(
        private ProductRepository $repository,
        private Validator $validator
    ) {
    }

    public function paginate(string $search = '', int $page = 1, int $perPage = 15): array
    {
        return $this->repository->findPage(
            searchColumns: ProductEntityDefinition::make()->searchable(),
            search: $search,
            page: $page,
            perPage: $perPage,
            orderBy: ProductEntityDefinition::make()->defaultOrder()
        );
    }

    public function find(int $id): ?array
    {
        return $this->repository->findById($id);
    }

    public function create(array $input): ValidationResult
    {
        $result = $this->validator->validate($input, [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'slug', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'summary' => ['string', 'max:2000'],
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
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'slug', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'summary' => ['string', 'max:2000'],
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
