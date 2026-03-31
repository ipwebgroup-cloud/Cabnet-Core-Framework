<?php
declare(strict_types=1);

final class AuthController
{
    public function loginForm(object $app, array $params = []): \Response
    {
        return $app->response()->html($app->renderer()->render('admin/login.php', [
            'appName' => $app->config('app.name', 'Cabnet Core'),
            'flashMessages' => $app->flash()->all(),
            'csrfToken' => $app->csrf()->token(),
        ]));
    }

    public function login(object $app, array $params = []): \Response
    {
        $csrfProtectLogin = (bool)$app->config('auth.csrf_protect_login', true);
        $loginRoute = (string)$app->config('auth.login_route', '/login');

        if ($csrfProtectLogin && !$app->csrf()->validate((string)$app->request()->input('_token', ''))) {
            $app->flash()->add('danger', 'Invalid login request token. Please try again.');
            return $app->response()->redirect($loginRoute);
        }

        $username = trim((string)$app->request()->input('username', ''));
        $password = trim((string)$app->request()->input('password', ''));

        $dbAuthEnabled = (bool)$app->config('framework.feature_flags.db_auth', false);

        if ($dbAuthEnabled) {
            try {
                /** @var \Cabnet\Application\Services\AdminAuthService $authService */
                $authService = $app->service('adminAuthService');
                if ($authService->attempt($username, $password)) {
                    $app->flash()->add('success', 'You are now signed in.');
                    return $app->response()->redirect('/');
                }
            } catch (\Throwable $e) {
                // fall through to configured fallback or invalid credentials response
            }
        }

        $allowStarterCredentials = (bool)$app->config('auth.allow_starter_credentials', false);

        if ($allowStarterCredentials && $username === 'admin' && $password === 'admin123') {
            $app->auth()->login([
                'name' => 'Administrator',
                'username' => 'admin',
                'role' => 'admin',
            ]);

            $app->flash()->add('warning', 'You are signed in using explicitly enabled starter credentials. Disable this fallback outside local development.');
            return $app->response()->redirect('/');
        }

        $app->flash()->add('danger', 'Invalid login credentials.');
        return $app->response()->redirect($loginRoute);
    }

    public function logout(object $app, array $params = []): \Response
    {
        $logoutRoute = (string)$app->config('auth.logout_route', '/logout');
        $logoutMethod = strtoupper((string)$app->config('auth.logout_method', 'POST'));
        $csrfProtectLogout = (bool)$app->config('auth.csrf_protect_logout', true);

        if ($app->request()->method() !== $logoutMethod) {
            return $app->response()->redirect('/');
        }

        if ($csrfProtectLogout && !$app->csrf()->validate((string)$app->request()->input('_token', ''))) {
            $app->flash()->add('danger', 'Invalid logout request token. Please try again.');
            return $app->response()->redirect('/');
        }

        $app->auth()->logout();
        $app->flash()->add('info', 'You have been signed out.');
        return $app->response()->redirect($app->config('auth.login_route', '/login'));
    }
}
