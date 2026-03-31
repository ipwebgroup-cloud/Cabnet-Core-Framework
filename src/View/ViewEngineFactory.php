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
            'twig' => new TwigRenderer($this->twigRoots()),
            'php' => new PhpRenderer($this->phpRoots()),
            default => new PhpRenderer($this->phpRoots()),
        };
    }

    /** @return array<string, string> */
    private function phpRoots(): array
    {
        return [
            'src' => $this->basePath . '/src/Presentation/Views/php',
            'app' => $this->basePath . '/app/Views/php',
        ];
    }

    /** @return array<string, string> */
    private function twigRoots(): array
    {
        return [
            'src' => $this->basePath . '/src/Presentation/Views/twig',
            'app' => $this->basePath . '/app/Views/twig',
        ];
    }
}
