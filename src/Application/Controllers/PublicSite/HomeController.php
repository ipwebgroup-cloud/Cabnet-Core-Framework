<?php
declare(strict_types=1);

namespace Cabnet\Application\Controllers\PublicSite;

use Cabnet\Http\Response;

final class HomeController extends \Cabnet\Application\Controllers\BaseController
{
    public function index(object $app, array $params = []): Response
    {
        return $this->render($app, 'public/home.php', [
            'appName' => $app->config('app.name', 'Cabnet Core'),
            'context' => $app->context(),
            'now' => $app->service('time'),
            'flashMessages' => $app->flash()->all(),
        ]);
    }
}
