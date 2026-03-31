<?php
declare(strict_types=1);

namespace Cabnet\View;

final class TemplateResolver
{
    /** @var array<string, string> */
    private array $basePaths;

    /** @param string|array<int|string, string> $basePath */
    public function __construct(string|array $basePath)
    {
        $this->basePaths = $this->normalizeBasePaths($basePath);
    }

    public function resolve(string $template): string
    {
        $normalized = ltrim(str_replace('\\', '/', $template), '/');
        [$explicitAlias, $relativeTemplate] = $this->parseAlias($normalized);

        foreach ($this->candidateRoots($explicitAlias) as $basePath) {
            $file = $basePath . '/' . $relativeTemplate;

            if (is_file($file)) {
                return $file;
            }
        }

        throw new ViewNotFoundException('View not found: ' . $normalized);
    }

    /** @param string|array<int|string, string> $basePath
     *  @return array<string, string>
     */
    private function normalizeBasePaths(string|array $basePath): array
    {
        if (is_string($basePath)) {
            return ['default' => rtrim($basePath, '/')];
        }

        $normalized = [];
        foreach ($basePath as $key => $value) {
            if (!is_string($value) || $value === '') {
                continue;
            }

            $alias = is_string($key) && $key !== '' ? $key : 'path_' . count($normalized);
            $normalized[$alias] = rtrim($value, '/');
        }

        return $normalized !== [] ? $normalized : ['default' => '.'];
    }

    /** @return array{0:?string,1:string} */
    private function parseAlias(string $template): array
    {
        if ($template === '' || $template[0] !== '@') {
            return [null, $template];
        }

        $withoutMarker = substr($template, 1);
        $parts = explode('/', $withoutMarker, 2);
        $alias = $parts[0] ?? '';
        $relative = $parts[1] ?? '';

        if ($alias === '' || $relative === '') {
            return [null, ltrim($withoutMarker, '/')];
        }

        return [$alias, ltrim($relative, '/')];
    }

    /** @return array<int, string> */
    private function candidateRoots(?string $explicitAlias): array
    {
        if ($explicitAlias === null) {
            return array_values($this->basePaths);
        }

        if (!isset($this->basePaths[$explicitAlias])) {
            return [];
        }

        return [$this->basePaths[$explicitAlias]];
    }
}
