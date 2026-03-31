<?php
declare(strict_types=1);

namespace Cabnet\Application\Controllers\Admin;

final class AuthController extends \Cabnet\Application\Controllers\BaseController
{
    public function loginForm(object $app, array $params = []): \Response
    {
        return $this->render($app, 'admin/login.php', [
            'appName' => $app->config('app.name', 'Cabnet Core'),
            'flashMessages' => $app->flash()->all(),
            'csrfToken' => $app->csrf()->token(),
        ]);
    }

    public function login(object $app, array $params = []): \Response
    {
        $csrfProtectLogin = (bool)$app->config('auth.csrf_protect_login', true);
        $loginRoute = (string)$app->config('auth.login_route', '/login');

        if ($csrfProtectLogin && !$app->csrf()->validate((string)$app->request()->input('_token', ''))) {
            $this->flash($app, 'danger', 'Invalid login request token. Please try again.');
            return $this->redirect($app, $loginRoute);
        }

        $username = trim((string)$app->request()->input('username', ''));
        $password = trim((string)$app->request()->input('password', ''));

        $dbAuthEnabled = (bool)$app->config('framework.feature_flags.db_auth', false);

        if ($dbAuthEnabled) {
            try {
                /** @var \Cabnet\Application\Services\AdminAuthService $authService */
                $authService = $app->service('adminAuthService');
                if ($authService->attempt($username, $password)) {
                    $this->flash($app, 'success', 'You are now signed in.');
                    return $this->redirect($app, '/');
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

            $this->flash($app, 'warning', 'You are signed in using explicitly enabled starter credentials. Disable this fallback outside local development.');
            return $this->redirect($app, '/');
        }

        $this->flash($app, 'danger', 'Invalid login credentials.');
        return $this->redirect($app, $loginRoute);
    }

    public function logout(object $app, array $params = []): \Response
    {
        $logoutMethod = strtoupper((string)$app->config('auth.logout_method', 'POST'));
        $csrfProtectLogout = (bool)$app->config('auth.csrf_protect_logout', true);

        if ($app->request()->method() !== $logoutMethod) {
            return $this->redirect($app, '/');
        }

        if ($csrfProtectLogout && !$app->csrf()->validate((string)$app->request()->input('_token', ''))) {
            $this->flash($app, 'danger', 'Invalid logout request token. Please try again.');
            return $this->redirect($app, '/');
        }

        $app->auth()->logout();
        $this->flash($app, 'info', 'You have been signed out.');
        return $this->redirect($app, $app->config('auth.login_route', '/login'));
    }
}
