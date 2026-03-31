<?php
declare(strict_types=1);

final class DashboardController extends BaseController
{
    public function index(App $app): Response
    {
        return $this->render($app, 'admin/dashboard.php', [
            'appName' => $app->config('app.name', 'Cabnet Core'),
            'context' => $app->context(),
            'now' => $app->service('time'),
            'flashMessages' => $app->flash()->all(),
            'user' => $app->auth()->user(),
        ]);
    }
}
