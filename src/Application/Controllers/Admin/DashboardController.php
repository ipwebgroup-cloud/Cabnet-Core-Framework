<?php
declare(strict_types=1);

namespace Cabnet\Application\Controllers\Admin;

final class DashboardController extends \Cabnet\Application\Controllers\BaseController
{
    public function index(object $app, array $params = []): \Response
    {
        return $this->render($app, 'admin/dashboard.php', [
            'appName' => $app->config('app.name', 'Cabnet Core'),
            'context' => $app->context(),
            'now' => $app->service('time'),
            'flashMessages' => $app->flash()->all(),
            'user' => $app->auth()->user(),
            'authUser' => $app->auth()->user(),
            'logoutAction' => $app->config('auth.logout_route', '/logout'),
            'logoutCsrfToken' => $app->csrf()->token(),
        ]);
    }
}
