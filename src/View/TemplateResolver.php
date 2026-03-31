<?php
declare(strict_types=1);

namespace Cabnet\View;

final class TemplateResolver
{
    public function __construct(
        private string $basePath
    ) {
        $this->basePath = rtrim($basePath, '/');
    }

    public function resolve(string $template): string
    {
        $normalized = ltrim(str_replace('\\', '/', $template), '/');
        $file = $this->basePath . '/' . $normalized;

        if (!is_file($file)) {
            throw new ViewNotFoundException('View not found: ' . $file);
        }

        return $file;
    }
}
