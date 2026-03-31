<?php
declare(strict_types=1);

namespace Cabnet\View;

class PhpRenderer implements Renderer
{
    private TemplateResolver $resolver;

    public function __construct(
        private string $basePath
    ) {
        $this->resolver = new TemplateResolver($basePath);
    }

    public function render(string $template, array $data = []): string
    {
        $file = $this->resolver->resolve($template);

        extract($data, EXTR_SKIP);

        ob_start();
        include $file;
        return (string) ob_get_clean();
    }
}
