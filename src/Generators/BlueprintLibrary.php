<?php
declare(strict_types=1);

namespace Cabnet\Generators;

use RuntimeException;

final class BlueprintLibrary
{
    /** @return array<string, array<string, mixed>> */
    public static function listExamples(string $basePath): array
    {
        $dir = rtrim($basePath, DIRECTORY_SEPARATOR) . '/blueprints/examples';
        if (!is_dir($dir)) {
            return [];
        }

        $examples = [];
        foreach (glob($dir . '/*.json') ?: [] as $file) {
            $slug = basename($file, '.json');
            $decoded = self::decodeFile($file);
            $examples[$slug] = [
                'example_name' => (string)($decoded['example_name'] ?? self::humanize($slug)),
                'example_summary' => (string)($decoded['example_summary'] ?? ''),
                'entity_key' => (string)($decoded['entity_key'] ?? ''),
                'feature_tags' => self::normalizeStringList($decoded['feature_tags'] ?? $decoded['example_tags'] ?? []),
                'path' => $file,
            ];
        }

        ksort($examples);
        return $examples;
    }

    public static function resolvePath(string $basePath, string $reference): string
    {
        if (str_starts_with($reference, 'example:')) {
            $slug = substr($reference, strlen('example:'));
            $path = rtrim($basePath, DIRECTORY_SEPARATOR) . '/blueprints/examples/' . $slug . '.json';
            if (!is_file($path)) {
                throw new RuntimeException(sprintf('Built-in blueprint example [%s] was not found.', $slug));
            }

            return $path;
        }

        return $reference;
    }

    /** @return array<string, mixed> */
    public static function load(string $basePath, string $reference): array
    {
        $path = self::resolvePath($basePath, $reference);
        if (!is_file($path)) {
            throw new RuntimeException(sprintf('Blueprint file not found: %s', $path));
        }

        return self::decodeFile($path);
    }

    /** @return array<string, mixed> */
    private static function decodeFile(string $path): array
    {
        $raw = file_get_contents($path);
        $decoded = json_decode((string) $raw, true);
        if (!is_array($decoded)) {
            throw new RuntimeException(sprintf('Invalid blueprint JSON in [%s].', $path));
        }

        return $decoded;
    }

    /** @return array<int, string> */
    private static function normalizeStringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (mixed $item): ?string => is_string($item) && $item !== '' ? $item : null,
            $value
        )));
    }

    private static function humanize(string $value): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $value));
    }
}
