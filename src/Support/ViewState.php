<?php
declare(strict_types=1);

namespace Cabnet\Support;

use Cabnet\Session\Session;

class ViewState
{
    public function __construct(private Session $session)
    {
    }

    public function old(?string $key = null, mixed $default = null): mixed
    {
        $old = $this->session->get('_old_input', []);

        if (!is_array($old)) {
            return $default;
        }

        if ($key === null) {
            return $old;
        }

        return $old[$key] ?? $default;
    }

    public function errors(?string $key = null): mixed
    {
        $errors = $this->session->get('_validation_errors', []);

        if (!is_array($errors)) {
            return $key === null ? [] : null;
        }

        if ($key === null) {
            return $errors;
        }

        return $errors[$key] ?? null;
    }

    public function firstError(string $key): ?string
    {
        $errors = $this->errors($key);
        return is_array($errors) ? ($errors[0] ?? null) : null;
    }

    public function putOld(array $data): void
    {
        $this->session->set('_old_input', $data);
    }

    public function putErrors(array $errors): void
    {
        $this->session->set('_validation_errors', $errors);
    }

    public function clearFormState(): void
    {
        $this->session->forget('_old_input');
        $this->session->forget('_validation_errors');
    }
}
