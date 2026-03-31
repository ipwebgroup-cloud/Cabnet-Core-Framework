<?php
declare(strict_types=1);

final class AdminAuthMiddleware implements MiddlewareInterface
{
    public function handle(App $app): ?Response
    {
        if ($app->context() !== 'admin') {
            return null;
        }

        $path = $app->request()->path();
        $loginRoute = $app->config('auth.login_route', '/login');

        if ($path === $loginRoute || $path === '/health') {
            return null;
        }

        if (!$app->auth()->check()) {
            $app->flash()->add('warning', 'Please sign in to access the admin area.');
            return $app->response()->redirect($loginRoute);
        }

        return null;
    }
}
