<?php
declare(strict_types=1);

namespace Cabnet\Application\Controllers\PublicSite;

final class HomeController
{
    public function index(object $app, array $params = []): \Response
    {
        $html = $app->renderer()->render('public/home.php', [
            'appName' => $app->config('app.name', 'Cabnet Core'),
            'context' => $app->context(),
            'now' => $app->service('time'),
            'flashMessages' => $app->flash()->all(),
        ]);

        return $app->response()->html($html);
    }
}
