<?php
declare(strict_types=1);

final class HealthController
{
    public function index(App $app): Response
    {
        return $app->response()->json([
            'status' => 'ok',
            'app' => $app->config('name', 'Cabnet Core'),
            'context' => $app->context(),
            'timestamp' => $app->service('time'),
        ]);
    }
}
