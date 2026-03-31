<?php
declare(strict_types=1);

abstract class FormRequest
{
    abstract public function rules(): array;

    public function validate(App $app): ValidationResult
    {
        return $app->validator()->validate($app->request()->all(), $this->rules());
    }
}
