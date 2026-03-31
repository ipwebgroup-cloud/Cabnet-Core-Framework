<?php

declare(strict_types=1);

namespace Cabnet\Generators;

final class BlueprintLibrary
{
    public static function examplesRoot(string $basePath): string
    {
        return rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'blueprints' . DIRECTORY_SEPARATOR . 'examples';
    }

    /**
     * @return array<string, array{path:string, entity_key:string, singular_label:string, plural_label:string, summary:string, view_engines:array<int,string>, feature_tags:array<int,string>}>
     */
    public static function listExamples(string $basePath): array
    {
        $root = self::examplesRoot($basePath);
        if (!is_dir($root)) {
            return [];
        }

        $examples = [];
        $files = glob($root . DIRECTORY_SEPARATOR . '*.json') ?: [];
        sort($files);

        foreach ($files as $file) {
            $data = self::decodeJsonFile($file);
            if ($data === null) {
                continue;
            }

            $name = basename($file, '.json');
            $examples[$name] = [
                'path' => $file,
                'entity_key' => (string)($data['entity_key'] ?? $name),
                'singular_label' => (string)($data['singular_label'] ?? self::studly(self::singularize($name))),
                'plural_label' => (string)($data['plural_label'] ?? self::studly($name)),
                'summary' => trim((string)($data['example_summary'] ?? 'Built-in scaffold example.')),
                'view_engines' => self::normalizeViewEngines($data),
                'feature_tags' => self::deriveFeatureTags($data),
            ];
        }

        ksort($examples);

        return $examples;
    }

    public static function resolvePath(string $basePath, string $input): ?string
    {
        $candidate = trim($input);
        if ($candidate === '') {
            return null;
        }

        if (is_file($candidate)) {
            return realpath($candidate) ?: $candidate;
        }

        $name = $candidate;
        if (str_starts_with($candidate, 'example:')) {
            $name = substr($candidate, strlen('example:'));
        }

        $name = trim($name);
        if ($name === '') {
            return null;
        }

        $name = preg_replace('/\.json$/i', '', $name) ?? $name;
        $builtInPath = self::examplesRoot($basePath) . DIRECTORY_SEPARATOR . $name . '.json';

        if (is_file($builtInPath)) {
            return $builtInPath;
        }

        return null;
    }

    /** @return array<string, mixed>|null */
    public static function load(string $basePath, string $input): ?array
    {
        $path = self::resolvePath($basePath, $input);
        if ($path === null) {
            return null;
        }

        return self::decodeJsonFile($path);
    }

    /** @return array<string, mixed>|null */
    private static function decodeJsonFile(string $file): ?array
    {
        $raw = file_get_contents($file);
        if ($raw === false) {
            return null;
        }

        $data = json_decode($raw, true);
        return is_array($data) ? $data : null;
    }

    /**
     * @param array<string, mixed> $blueprint
     * @return array<int, string>
     */
    private static function normalizeViewEngines(array $blueprint): array
    {
        $raw = $blueprint['view_engines'] ?? ($blueprint['view_engine'] ?? ['php']);
        if (is_string($raw)) {
            $raw = [$raw];
        }

        $engines = array_values(array_unique(array_filter(array_map(
            static fn (mixed $engine): string => is_string($engine) ? strtolower(trim($engine)) : '',
            (array)$raw
        ))));

        return $engines === [] ? ['php'] : $engines;
    }

    /**
     * @param array<string, mixed> $blueprint
     * @return array<int, string>
     */
    private static function deriveFeatureTags(array $blueprint): array
    {
        $tags = [];
        $fields = is_array($blueprint['fields'] ?? null) ? $blueprint['fields'] : [];

        foreach ($fields as $meta) {
            if (!is_array($meta)) {
                continue;
            }

            $type = strtolower((string)($meta['type'] ?? 'text'));

            if ($type === 'file' || $type === 'image' || !empty($meta['upload'])) {
                $tags['uploads'] = 'uploads';
            }

            if (!empty($meta['translatable'])) {
                $tags['translatable'] = 'translatable';
            }

            if (isset($meta['relation']) && is_array($meta['relation'])) {
                $tags['relations'] = 'relations';
            }

            if (array_key_exists('filter', $meta) || array_key_exists('filterable', $meta) || array_key_exists('list_filter', $meta)) {
                $tags['filters'] = 'filters';
            }
        }

        if (!empty($blueprint['policy_class']) || !empty($blueprint['policy'])) {
            $tags['policy'] = 'policy';
        }

        if (is_array($blueprint['permissions'] ?? null) || !empty($blueprint['access_roles'])) {
            $tags['permissions'] = 'permissions';
        }

        if (count(self::normalizeViewEngines($blueprint)) > 1 || in_array('twig', self::normalizeViewEngines($blueprint), true)) {
            $tags['twig'] = 'twig';
        }

        return array_values($tags);
    }

    private static function singularize(string $value): string
    {
        return str_ends_with($value, 's') ? substr($value, 0, -1) : $value;
    }

    private static function studly(string $value): string
    {
        $value = str_replace(['-', '_'], ' ', strtolower($value));
        return str_replace(' ', '', ucwords($value));
    }
}
