<?php
declare(strict_types=1);

namespace Cabnet\Generators;

use InvalidArgumentException;

final class BlueprintValidator
{
    /**
     * @param array<string, mixed> $blueprint
     * @return array<int, string>
     */
    public static function validate(array $blueprint): array
    {
        $errors = [];

        foreach (['entity_key', 'singular_label', 'plural_label', 'table'] as $key) {
            $value = $blueprint[$key] ?? null;
            if (!is_string($value) || trim($value) === '') {
                $errors[] = sprintf('Blueprint is missing required string [%s].', $key);
            }
        }

        $fields = $blueprint['fields'] ?? null;
        if (!is_array($fields) || $fields === []) {
            $errors[] = 'Blueprint must define a non-empty [fields] object.';
            return $errors;
        }

        foreach ($fields as $fieldName => $fieldMeta) {
            if (!is_string($fieldName) || $fieldName === '') {
                $errors[] = 'Blueprint fields must use non-empty string keys.';
                continue;
            }

            if (!is_array($fieldMeta)) {
                $errors[] = sprintf('Field [%s] metadata must be an object.', $fieldName);
                continue;
            }

            $type = $fieldMeta['type'] ?? null;
            if (!is_string($type) || trim($type) === '') {
                $errors[] = sprintf('Field [%s] must declare a non-empty string [type].', $fieldName);
            }

            $label = $fieldMeta['label'] ?? null;
            if ($label !== null && !is_string($label)) {
                $errors[] = sprintf('Field [%s] label must be a string when provided.', $fieldName);
            }

            if (!empty($fieldMeta['translatable'])) {
                $locales = $fieldMeta['locales'] ?? null;
                if (!is_array($locales) || $locales === []) {
                    $errors[] = sprintf('Translatable field [%s] must define a non-empty [locales] array.', $fieldName);
                }
            }

            if (!empty($fieldMeta['relation'])) {
                if (!is_array($fieldMeta['relation'])) {
                    $errors[] = sprintf('Field [%s] relation metadata must be an object.', $fieldName);
                } else {
                    foreach (['table', 'value_column', 'label_column'] as $relationKey) {
                        $relationValue = $fieldMeta['relation'][$relationKey] ?? null;
                        if (!is_string($relationValue) || trim($relationValue) === '') {
                            $errors[] = sprintf('Relation field [%s] is missing required string [%s].', $fieldName, $relationKey);
                        }
                    }
                }
            }

            if (!empty($fieldMeta['upload']) || in_array($type, ['image', 'file'], true)) {
                $maxSize = $fieldMeta['max_size_kb'] ?? null;
                if ($maxSize !== null && (!is_int($maxSize) && !(is_string($maxSize) && ctype_digit($maxSize)))) {
                    $errors[] = sprintf('Upload field [%s] max_size_kb must be an integer when provided.', $fieldName);
                }
            }

            if (isset($fieldMeta['options']) && !is_array($fieldMeta['options'])) {
                $errors[] = sprintf('Field [%s] options must be an object or array when provided.', $fieldName);
            }

            foreach (['filter', 'list_filter'] as $filterKey) {
                if (isset($fieldMeta[$filterKey]) && !is_array($fieldMeta[$filterKey]) && !is_bool($fieldMeta[$filterKey])) {
                    $errors[] = sprintf('Field [%s] %s must be either a boolean or an object.', $fieldName, $filterKey);
                }
            }
        }

        $viewEngines = $blueprint['view_engines'] ?? ($blueprint['view_engine'] ?? null);
        if ($viewEngines !== null) {
            $engines = is_array($viewEngines) ? $viewEngines : [$viewEngines];
            foreach ($engines as $engine) {
                if (!is_string($engine) || !in_array($engine, ['php', 'twig'], true)) {
                    $errors[] = 'Blueprint view engines may only contain [php] and/or [twig].';
                    break;
                }
            }
        }

        if (isset($blueprint['filters']) && !is_array($blueprint['filters'])) {
            $errors[] = 'Blueprint [filters] must be an object when provided.';
        }

        if (isset($blueprint['permissions']) && !is_array($blueprint['permissions'])) {
            $errors[] = 'Blueprint [permissions] must be an object when provided.';
        }

        return $errors;
    }

    /** @param array<string, mixed> $blueprint */
    public static function assertValid(array $blueprint): void
    {
        $errors = self::validate($blueprint);
        if ($errors !== []) {
            throw new InvalidArgumentException("Blueprint validation failed:\n- " . implode("\n- ", $errors));
        }
    }
}
