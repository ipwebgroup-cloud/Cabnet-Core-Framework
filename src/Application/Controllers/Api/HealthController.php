<?php
declare(strict_types=1);

namespace Cabnet\Application\Controllers\Api;

use Cabnet\Http\Response;

final class HealthController
{
    public function index(object $app, array $params = []): Response
    {
        return $app->response()->json([
            'status' => 'ok',
            'app' => $app->config('app.name', 'Cabnet Core'),
            'context' => $app->context(),
            'timestamp' => $app->service('time'),
            'framework_version' => $app->config('framework.version', 'unknown'),
        ]);
    }
}
