<?php
declare(strict_types=1);

final class Validator
{
    public function validate(array $input, array $rules): ValidationResult
    {
        $errors = [];
        $clean = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $input[$field] ?? null;
            $fieldRules = is_array($fieldRules) ? $fieldRules : explode('|', (string)$fieldRules);

            $isRequired = in_array('required', $fieldRules, true);

            if ($this->hasRule($fieldRules, 'translatable')) {
                [$fieldErrors, $cleanValue] = $this->validateTranslatableField($field, $value, $fieldRules, $isRequired);
                if ($fieldErrors !== []) {
                    foreach ($fieldErrors as $key => $messages) {
                        foreach ($messages as $message) {
                            $errors[$key][] = $message;
                        }
                    }
                    continue;
                }

                $clean[$field] = $cleanValue;
                continue;
            }

            if ($this->hasRule($fieldRules, 'upload')) {
                [$fieldErrors, $cleanValue] = $this->validateUploadField($field, $value, $fieldRules, $isRequired);
                if ($fieldErrors !== []) {
                    foreach ($fieldErrors as $key => $messages) {
                        foreach ($messages as $message) {
                            $errors[$key][] = $message;
                        }
                    }
                    continue;
                }

                $clean[$field] = $cleanValue;
                continue;
            }

            if ($isRequired && $this->isEmptyValue($value)) {
                $errors[$field][] = 'This field is required.';
                continue;
            }

            if ($this->isEmptyValue($value) && !$isRequired) {
                $clean[$field] = $value;
                continue;
            }

            foreach ($fieldRules as $rule) {
                if ($rule === 'required') {
                    continue;
                }

                if ($rule === 'string' && !is_string($value)) {
                    $errors[$field][] = 'This field must be a string.';
                }

                if ($rule === 'integer' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $errors[$field][] = 'This field must be an integer.';
                }

                if ($rule === 'email' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                    $errors[$field][] = 'This field must be a valid email.';
                }

                if ($rule === 'slug' && !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', (string)$value)) {
                    $errors[$field][] = 'This field must be a valid slug.';
                }

                if (str_starts_with((string)$rule, 'in:')) {
                    $allowed = array_values(array_filter(array_map('trim', explode(',', substr((string)$rule, 3))), static fn (string $item): bool => $item !== ''));
                    if ($allowed !== [] && !in_array((string)$value, $allowed, true)) {
                        $errors[$field][] = 'This field must be one of: ' . implode(', ', $allowed) . '.';
                    }
                }

                if (str_starts_with((string)$rule, 'max:')) {
                    $max = (int)substr((string)$rule, 4);
                    $length = $this->stringLength((string)$value);
                    if ($length > $max) {
                        $errors[$field][] = 'This field exceeds the maximum length of ' . $max . '.';
                    }
                }

                if (str_starts_with((string)$rule, 'min:')) {
                    $min = (int)substr((string)$rule, 4);
                    $length = $this->stringLength((string)$value);
                    if ($length < $min) {
                        $errors[$field][] = 'This field must be at least ' . $min . ' characters.';
                    }
                }
            }

            if (in_array('integer', $fieldRules, true) && filter_var($value, FILTER_VALIDATE_INT) !== false) {
                $clean[$field] = (int)$value;
                continue;
            }

            $clean[$field] = is_string($value) ? trim($value) : $value;
        }

        return new ValidationResult(empty($errors), $errors, $clean);
    }

    /**
     * @param array<int, string> $fieldRules
     * @return array{0: array<string, array<int, string>>, 1: array<string, string>}
     */
    private function validateTranslatableField(string $field, mixed $value, array $fieldRules, bool $isRequired): array
    {
        $errors = [];
        $value = is_array($value) ? $value : [];

        $locales = $this->ruleValues($fieldRules, 'locales');
        if ($locales === []) {
            $locales = array_keys($value);
        }
        if ($locales === []) {
            $locales = ['en'];
        }

        $requiredLocales = $this->ruleValues($fieldRules, 'required_locales');
        if ($requiredLocales === [] && $isRequired) {
            $requiredLocales = $locales;
        }

        $min = $this->ruleNumber($fieldRules, 'min');
        $max = $this->ruleNumber($fieldRules, 'max');

        $clean = [];
        $nonEmptyCount = 0;

        foreach ($locales as $locale) {
            $localeValue = $value[$locale] ?? '';
            $localeValue = is_string($localeValue) ? trim($localeValue) : '';

            if ($localeValue !== '') {
                $nonEmptyCount++;
            }

            if (in_array($locale, $requiredLocales, true) && $localeValue === '') {
                $errors[$field . '.' . $locale][] = 'This locale is required.';
                continue;
            }

            if ($localeValue !== '' && $min !== null && $this->stringLength($localeValue) < $min) {
                $errors[$field . '.' . $locale][] = 'This field must be at least ' . $min . ' characters.';
            }

            if ($localeValue !== '' && $max !== null && $this->stringLength($localeValue) > $max) {
                $errors[$field . '.' . $locale][] = 'This field exceeds the maximum length of ' . $max . '.';
            }

            $clean[$locale] = $localeValue;
        }

        if ($isRequired && $nonEmptyCount == 0) {
            $errors[$field][] = 'This field is required.';
        }

        return [$errors, $clean];
    }

    /**
     * @param array<int, string> $fieldRules
     * @return array{0: array<string, array<int, string>>, 1: mixed}
     */
    private function validateUploadField(string $field, mixed $value, array $fieldRules, bool $isRequired): array
    {
        $errors = [];

        if ($this->isEmptyValue($value)) {
            if ($isRequired) {
                $errors[$field][] = 'This field is required.';
            }

            return [$errors, $value];
        }

        if (!is_array($value) || !isset($value['tmp_name'], $value['name'], $value['error'], $value['size'])) {
            $errors[$field][] = 'This field must be a file upload.';
            return [$errors, $value];
        }

        $errorCode = (int)($value['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($errorCode !== UPLOAD_ERR_OK) {
            $errors[$field][] = 'The uploaded file is invalid.';
            return [$errors, $value];
        }

        if ($this->hasRule($fieldRules, 'image')) {
            $name = strtolower((string)($value['name'] ?? ''));
            $type = strtolower((string)($value['type'] ?? ''));
            $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/', $name) === 1 || str_starts_with($type, 'image/');
            if (!$isImage) {
                $errors[$field][] = 'This field must be an image upload.';
            }
        }

        $maxKb = $this->ruleNumber($fieldRules, 'file_max_kb');
        if ($maxKb !== null && (int)($value['size'] ?? 0) > ($maxKb * 1024)) {
            $errors[$field][] = 'This file exceeds the maximum size of ' . $maxKb . ' KB.';
        }

        return [$errors, $value];
    }

    /** @param array<int, string> $rules */
    private function hasRule(array $rules, string $needle): bool
    {
        foreach ($rules as $rule) {
            if ($rule === $needle || str_starts_with($rule, $needle . ':')) {
                return true;
            }
        }
        return false;
    }

    /** @param array<int, string> $rules
     * @return array<int, string>
     */
    private function ruleValues(array $rules, string $prefix): array
    {
        foreach ($rules as $rule) {
            if (!str_starts_with($rule, $prefix . ':')) {
                continue;
            }
            return array_values(array_filter(array_map('trim', explode(',', substr((string)$rule, strlen($prefix) + 1))), static fn (string $item): bool => $item !== ''));
        }
        return [];
    }

    /** @param array<int, string> $rules */
    private function ruleNumber(array $rules, string $prefix): ?int
    {
        foreach ($rules as $rule) {
            if (!str_starts_with($rule, $prefix . ':')) {
                continue;
            }
            return (int)substr((string)$rule, strlen($prefix) + 1);
        }
        return null;
    }

    private function isEmptyValue(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }
        if (is_array($value) && isset($value['tmp_name'], $value['error'])) {
            return ((string)($value['tmp_name'] ?? '') === '') || ((int)($value['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE);
        }
        return false;
    }

    private function stringLength(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    }
}
