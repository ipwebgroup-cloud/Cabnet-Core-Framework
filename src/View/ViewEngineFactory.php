<?php
declare(strict_types=1);

namespace Cabnet\View;

final class ViewEngineFactory
{
    public function __construct(
        private string $basePath,
        private string $engine = 'php'
    ) {
    }

    public function make(): Renderer
    {
        return match ($this->engine) {
            'twig' => new TwigRenderer($this->basePath . '/app/Views/twig'),
            'php' => new PhpRenderer($this->basePath . '/app/Views/php'),
            default => new PhpRenderer($this->basePath . '/app/Views/php'),
        };
    }
}
