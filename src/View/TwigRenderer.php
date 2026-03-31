<?php
declare(strict_types=1);

namespace Cabnet\View;

use RuntimeException;

class TwigRenderer implements Renderer
{
    public function __construct(
        private string $basePath
    ) {
    }

    public function render(string $template, array $data = []): string
    {
        if (!class_exists('\Twig\Environment') || !class_exists('\Twig\Loader\FilesystemLoader')) {
            throw new RuntimeException(
                'Twig is not installed. Add twig/twig via Composer or switch renderer to PhpRenderer.'
            );
        }

        $loader = new \Twig\Loader\FilesystemLoader($this->basePath);
        $twig = new \Twig\Environment($loader, [
            'cache' => false,
            'autoescape' => 'html',
        ]);

        return $twig->render($template, $data);
    }
}
