<?php
declare(strict_types=1);

namespace Cabnet\View;

interface Renderer
{
    public function render(string $template, array $data = []): string;
}
