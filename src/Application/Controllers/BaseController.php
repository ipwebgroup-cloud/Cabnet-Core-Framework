<?php
declare(strict_types=1);

namespace Cabnet\Application\Controllers;

abstract class BaseController
{
    protected function render(object $app, string $template, array $data = []): \Response
    {
        return $app->response()->html($app->renderer()->render($template, $data));
    }

    protected function redirect(object $app, string $to): \Response
    {
        return $app->response()->redirect($to);
    }

    protected function flash(object $app, string $type, string $message): void
    {
        $app->flash()->add($type, $message);
    }
}
