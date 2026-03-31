<?php
declare(strict_types=1);

namespace Cabnet\View;

use RuntimeException;

class TwigRenderer implements Renderer
{
    /** @var array<string, string> */
    private array $basePaths;

    /** @param string|array<int|string, string> $basePath */
    public function __construct(string|array $basePath)
    {
        $this->basePaths = $this->normalizeBasePaths($basePath);
    }

    public function render(string $template, array $data = []): string
    {
        if (!class_exists('\Twig\Environment') || !class_exists('\Twig\Loader\FilesystemLoader')) {
            throw new RuntimeException(
                'Twig is not installed. Add twig/twig via Composer or switch renderer to PhpRenderer.'
            );
        }

        $loader = new \Twig\Loader\FilesystemLoader();
        $registered = false;
        foreach ($this->basePaths as $alias => $path) {
            if (!is_dir($path)) {
                continue;
            }

            $loader->addPath($path);
            if ($alias !== 'default') {
                $loader->addPath($path, $alias);
            }
            $registered = true;
        }

        if (!$registered) {
            throw new RuntimeException('No Twig view directories were found for the configured renderer roots.');
        }

        $twig = new \Twig\Environment($loader, [
            'cache' => false,
            'autoescape' => 'html',
        ]);

        return $twig->render(self::normalizeLogicalTemplate($template), $data);
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

    public static function normalizeLogicalTemplate(string $template): string
    {
        $normalized = str_replace('\\', '/', trim($template));

        if (str_ends_with($normalized, '.php')) {
            return substr($normalized, 0, -4) . '.twig';
        }

        $pathWithoutAlias = $normalized;
        if (str_starts_with($normalized, '@')) {
            $withoutMarker = substr($normalized, 1);
            $parts = explode('/', $withoutMarker, 2);
            $pathWithoutAlias = $parts[1] ?? $parts[0] ?? '';
        }

        if ($pathWithoutAlias !== '' && pathinfo($pathWithoutAlias, PATHINFO_EXTENSION) === '') {
            return $normalized . '.twig';
        }

        return $normalized;
    }
}
