<?php
declare(strict_types=1);

namespace Cabnet\Application\Controllers\Admin;

use Cabnet\Application\Controllers\BaseController;
use Cabnet\Application\Crud\CrudEntityDefinition;
use Cabnet\Application\Crud\CrudModuleRegistry;
use Cabnet\Http\Response;

abstract class BaseCrudController extends BaseController
{
    abstract protected function moduleKey(): string;

    protected function registry(object $app): CrudModuleRegistry
    {
        /** @var CrudModuleRegistry $registry */
        $registry = $app->service('crudModuleRegistry');
        return $registry;
    }

    protected function entityDefinition(object $app): CrudEntityDefinition
    {
        return $this->registry($app)->definition($this->moduleKey());
    }

    /** @return array<string, mixed> */
    protected function moduleMeta(object $app): array
    {
        return $this->registry($app)->meta($this->moduleKey());
    }

    protected function routeBaseName(object $app): string
    {
        return $this->registry($app)->adminRouteBase($this->moduleKey());
    }

    protected function viewPath(object $app, string $view): string
    {
        return $this->registry($app)->adminViewPath($this->moduleKey()) . '/' . ltrim($view, '/');
    }

    protected function crudService(object $app): object
    {
        return $app->service($this->registry($app)->crudService($this->moduleKey()));
    }

    protected function singularLabel(object $app): string
    {
        return $this->registry($app)->singularLabel($this->moduleKey());
    }

    /** @return array<string, mixed> */
    protected function inputFromRequest(object $app): array
    {
        $payload = [];

        foreach ($this->entityDefinition($app)->inputDefaults() as $field => $default) {
            $payload[$field] = $app->request()->input($field, $default);
        }

        return $payload;
    }

    public function index(object $app, array $params = []): Response
    {
        $service = $this->crudService($app);
        $search = trim((string)$app->request()->query('q', ''));
        $page = (int)$app->request()->query('page', 1);

        $pageData = $service->paginate($search, $page, 10);

        return $this->render($app, $this->viewPath($app, 'index.php'), $this->listViewData(
            $app,
            $pageData,
            $search
        ));
    }

    public function createForm(object $app, array $params = []): Response
    {
        return $this->render($app, $this->viewPath($app, 'create.php'), $this->formViewData(
            $app,
            'create',
            $app->url()->route($this->routeBaseName($app) . '.store'),
            $app->url()->route($this->routeBaseName($app) . '.index')
        ));
    }

    public function store(object $app, array $params = []): Response
    {
        if (!$app->csrf()->validate((string)$app->request()->input('_token', ''))) {
            $this->flash($app, 'danger', 'Invalid CSRF token.');
            return $this->redirect($app, $app->url()->route($this->routeBaseName($app) . '.create'));
        }

        $service = $this->crudService($app);
        $input = $this->inputFromRequest($app);
        $result = $service->create($input);

        if (!$result->valid()) {
            $app->viewState()->putOld($input);
            $app->viewState()->putErrors($result->errors());
            $this->flash($app, 'danger', 'Please correct the form errors.');
            return $this->redirect($app, $app->url()->route($this->routeBaseName($app) . '.create'));
        }

        $app->viewState()->clearFormState();
        $this->flash($app, 'success', $this->singularLabel($app) . ' created successfully.');
        return $this->redirect($app, $app->url()->route($this->routeBaseName($app) . '.index'));
    }

    public function editForm(object $app, array $params = []): Response
    {
        $id = (int)($params['id'] ?? 0);
        $service = $this->crudService($app);
        $row = $service->find($id);

        if (!$row) {
            $this->flash($app, 'warning', $this->singularLabel($app) . ' not found.');
            return $this->redirect($app, $app->url()->route($this->routeBaseName($app) . '.index'));
        }

        return $this->render($app, $this->viewPath($app, 'edit.php'), $this->formViewData(
            $app,
            'edit',
            $app->url()->route($this->routeBaseName($app) . '.update', ['id' => $id]),
            $app->url()->route($this->routeBaseName($app) . '.index'),
            $row
        ));
    }

    public function update(object $app, array $params = []): Response
    {
        $id = (int)($params['id'] ?? 0);

        if (!$app->csrf()->validate((string)$app->request()->input('_token', ''))) {
            $this->flash($app, 'danger', 'Invalid CSRF token.');
            return $this->redirect($app, $app->url()->route($this->routeBaseName($app) . '.edit', ['id' => $id]));
        }

        $service = $this->crudService($app);
        $row = $service->find($id);

        if (!$row) {
            $this->flash($app, 'warning', $this->singularLabel($app) . ' not found.');
            return $this->redirect($app, $app->url()->route($this->routeBaseName($app) . '.index'));
        }

        $input = $this->inputFromRequest($app);
        $result = $service->update($id, $input);

        if (!$result->valid()) {
            $app->viewState()->putOld($input);
            $app->viewState()->putErrors($result->errors());
            $this->flash($app, 'danger', 'Please correct the form errors.');
            return $this->redirect($app, $app->url()->route($this->routeBaseName($app) . '.edit', ['id' => $id]));
        }

        $app->viewState()->clearFormState();
        $this->flash($app, 'success', $this->singularLabel($app) . ' updated successfully.');
        return $this->redirect($app, $app->url()->route($this->routeBaseName($app) . '.index'));
    }

    public function destroy(object $app, array $params = []): Response
    {
        $id = (int)($params['id'] ?? 0);

        if (!$app->csrf()->validate((string)$app->request()->input('_token', ''))) {
            $this->flash($app, 'danger', 'Invalid CSRF token.');
            return $this->redirect($app, $app->url()->route($this->routeBaseName($app) . '.index'));
        }

        $service = $this->crudService($app);
        $row = $service->find($id);

        if (!$row) {
            $this->flash($app, 'warning', $this->singularLabel($app) . ' not found.');
            return $this->redirect($app, $app->url()->route($this->routeBaseName($app) . '.index'));
        }

        $service->delete($id);
        $this->flash($app, 'success', $this->singularLabel($app) . ' deleted successfully.');
        return $this->redirect($app, $app->url()->route($this->routeBaseName($app) . '.index'));
    }

    protected function listViewData(
        object $app,
        array $pageData,
        string $search
    ): array {
        $routeBaseName = $this->routeBaseName($app);
        $paginator = new \Paginator(
            (int)$pageData['page'],
            (int)$pageData['per_page'],
            (int)$pageData['total']
        );

        return [
            'appName' => $app->config('app.name', 'Cabnet Core'),
            'flashMessages' => $app->flash()->all(),
            'definition' => $this->entityDefinition($app),
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
            'definition' => $this->entityDefinition($app),
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
