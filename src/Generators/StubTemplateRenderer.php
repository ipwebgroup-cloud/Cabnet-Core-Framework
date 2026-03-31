<?php
declare(strict_types=1);

namespace Cabnet\Generators;

final class StubTemplateRenderer
{
    public function render(string $template, array $vars = []): string
    {
        $replacements = [];
        foreach ($vars as $key => $value) {
            $replacements['{{' . $key . '}}'] = (string)$value;
        }

        return strtr($template, $replacements);
    }
}
