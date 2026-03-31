<?php
declare(strict_types=1);

namespace Cabnet\Application\Controllers;

use Cabnet\Http\Response;

abstract class BaseController
{
    protected function render(object $app, string $template, array $data = []): Response
    {
        $defaults = [
            'flashMessages' => $app->flash()->all(),
            'authUser' => $app->auth()->user(),
            'logoutAction' => $app->config('auth.logout_route', '/logout'),
            'logoutCsrfToken' => $app->csrf()->token(),
            'currentPath' => $app->request()->path(),
            'adminMenuItems' => $this->adminMenuItems(),
        ];

        return $app->response()->html($app->renderer()->render($template, array_replace($defaults, $data)));
    }

    protected function redirect(object $app, string $to): Response
    {
        return $app->response()->redirect($to);
    }

    protected function flash(object $app, string $type, string $message): void
    {
        $app->flash()->add($type, $message);
    }

    /** @return array<int, array<string, mixed>> */
    private function adminMenuItems(): array
    {
        $path = BASE_PATH . '/config/admin_menu.php';
        if (!is_file($path)) {
            return [];
        }

        $loaded = require $path;
        return is_array($loaded) ? $loaded : [];
    }
}
