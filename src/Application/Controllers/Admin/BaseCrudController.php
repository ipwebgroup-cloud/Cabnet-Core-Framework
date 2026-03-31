<?php
declare(strict_types=1);

namespace Cabnet\Application\Controllers\Admin;

use Cabnet\Application\Controllers\BaseController;

abstract class BaseCrudController extends BaseController
{
    abstract protected function entityDefinition(): \Cabnet\Application\Crud\CrudEntityDefinition;

    protected function listViewData(
        object $app,
        array $pageData,
        string $search,
        string $routeBaseName
    ): array {
        $paginator = new \Paginator(
            (int)$pageData['page'],
            (int)$pageData['per_page'],
            (int)$pageData['total']
        );

        return [
            'appName' => $app->config('app.name', 'Cabnet Core'),
            'flashMessages' => $app->flash()->all(),
            'definition' => $this->entityDefinition(),
            'rows' => $pageData['rows'],
            'search' => $search,
            'paginator' => $paginator,
            'listPath' => $app->url()->route($routeBaseName . '.index'),
            'createPath' => $app->url()->route($routeBaseName . '.create'),
            'editRouteName' => $routeBaseName . '.edit',
            'deleteRouteName' => $routeBaseName . '.delete',
            'csrfToken' => $app->csrf()->token(),
            'urlService' => $app->url(),
            'authUser' => $app->auth()->user(),
            'logoutAction' => $app->config('auth.logout_route', '/logout'),
            'logoutCsrfToken' => $app->csrf()->token(),
        ];
    }

    protected function formViewData(
        object $app,
        string $mode,
        string $formAction,
        string $backPath,
        array $row = []
    ): array {
        return [
            'appName' => $app->config('app.name', 'Cabnet Core'),
            'flashMessages' => $app->flash()->all(),
            'definition' => $this->entityDefinition(),
            'csrfToken' => $app->csrf()->token(),
            'old' => $app->viewState()->old(),
            'errors' => $app->viewState()->errors(),
            'row' => $row,
            'mode' => $mode,
            'formAction' => $formAction,
            'backPath' => $backPath,
            'urlService' => $app->url(),
            'authUser' => $app->auth()->user(),
            'logoutAction' => $app->config('auth.logout_route', '/logout'),
            'logoutCsrfToken' => $app->csrf()->token(),
        ];
    }
}
