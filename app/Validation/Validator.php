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

            if ($isRequired && ($value === null || $value === '')) {
                $errors[$field][] = 'This field is required.';
                continue;
            }

            if (($value === null || $value === '') && !$isRequired) {
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

                if (str_starts_with((string)$rule, 'max:')) {
                    $max = (int)substr((string)$rule, 4);
                    if (mb_strlen((string)$value) > $max) {
                        $errors[$field][] = 'This field exceeds the maximum length of ' . $max . '.';
                    }
                }

                if (str_starts_with((string)$rule, 'min:')) {
                    $min = (int)substr((string)$rule, 4);
                    if (mb_strlen((string)$value) < $min) {
                        $errors[$field][] = 'This field must be at least ' . $min . ' characters.';
                    }
                }
            }

            $clean[$field] = is_string($value) ? trim($value) : $value;
        }

        return new ValidationResult(empty($errors), $errors, $clean);
    }
}
