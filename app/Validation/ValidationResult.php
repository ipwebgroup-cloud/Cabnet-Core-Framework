<?php
declare(strict_types=1);

final class ValidationResult
{
    public function __construct(
        private bool $valid,
        private array $errors,
        private array $data
    ) {
    }

    public function valid(): bool
    {
        return $this->valid;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }
}
