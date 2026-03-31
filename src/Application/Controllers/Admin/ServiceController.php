<?php
declare(strict_types=1);

namespace Cabnet\Application\Controllers\Admin;

use Cabnet\Http\Response;

use Cabnet\Application\Crud\Definitions\ServiceEntityDefinition;
use Cabnet\Application\Services\ServiceCrudService;

final class ServiceController extends BaseCrudController
{
    protected function entityDefinition(): \Cabnet\Application\Crud\CrudEntityDefinition
    {
        return ServiceEntityDefinition::make();
    }

    public function index(object $app, array $params = []): Response
    {
        /** @var ServiceCrudService $service */
        $service = $app->service('serviceCrud');
        $search = trim((string)$app->request()->query('q', ''));
        $page = (int)$app->request()->query('page', 1);

        $pageData = $service->paginate($search, $page, 10);

        return $this->render($app, 'admin/services/index.php', $this->listViewData(
            $app,
            $pageData,
            $search,
            'admin.services'
        ));
    }

    public function createForm(object $app, array $params = []): Response
    {
        return $this->render($app, 'admin/services/create.php', $this->formViewData(
            $app,
            'create',
            $app->url()->route('admin.services.store'),
            $app->url()->route('admin.services.index')
        ));
    }

    public function store(object $app, array $params = []): Response
    {
        if (!$app->csrf()->validate((string)$app->request()->input('_token', ''))) {
            $this->flash($app, 'danger', 'Invalid CSRF token.');
            return $this->redirect($app, $app->url()->route('admin.services.create'));
        }

        /** @var ServiceCrudService $service */
        $service = $app->service('serviceCrud');

        $input = [
            'title' => $app->request()->input('title', ''),
            'slug' => $app->request()->input('slug', ''),
            'status' => $app->request()->input('status', 'draft'),
            'summary' => $app->request()->input('summary', ''),
        ];

        $result = $service->create($input);

        if (!$result->valid()) {
            $app->viewState()->putOld($input);
            $app->viewState()->putErrors($result->errors());
            $this->flash($app, 'danger', 'Please correct the form errors.');
            return $this->redirect($app, $app->url()->route('admin.services.create'));
        }

        $app->viewState()->clearFormState();
        $this->flash($app, 'success', 'Service created successfully.');
        return $this->redirect($app, $app->url()->route('admin.services.index'));
    }

    public function editForm(object $app, array $params = []): Response
    {
        $id = (int)($params['id'] ?? 0);

        /** @var ServiceCrudService $service */
        $service = $app->service('serviceCrud');
        $row = $service->find($id);

        if (!$row) {
            $this->flash($app, 'warning', 'Service not found.');
            return $this->redirect($app, $app->url()->route('admin.services.index'));
        }

        return $this->render($app, 'admin/services/edit.php', $this->formViewData(
            $app,
            'edit',
            $app->url()->route('admin.services.update', ['id' => $id]),
            $app->url()->route('admin.services.index'),
            $row
        ));
    }

    public function update(object $app, array $params = []): Response
    {
        $id = (int)($params['id'] ?? 0);

        if (!$app->csrf()->validate((string)$app->request()->input('_token', ''))) {
            $this->flash($app, 'danger', 'Invalid CSRF token.');
            return $this->redirect($app, $app->url()->route('admin.services.edit', ['id' => $id]));
        }

        /** @var ServiceCrudService $service */
        $service = $app->service('serviceCrud');
        $row = $service->find($id);

        if (!$row) {
            $this->flash($app, 'warning', 'Service not found.');
            return $this->redirect($app, $app->url()->route('admin.services.index'));
        }

        $input = [
            'title' => $app->request()->input('title', ''),
            'slug' => $app->request()->input('slug', ''),
            'status' => $app->request()->input('status', 'draft'),
            'summary' => $app->request()->input('summary', ''),
        ];

        $result = $service->update($id, $input);

        if (!$result->valid()) {
            $app->viewState()->putOld($input);
            $app->viewState()->putErrors($result->errors());
            $this->flash($app, 'danger', 'Please correct the form errors.');
            return $this->redirect($app, $app->url()->route('admin.services.edit', ['id' => $id]));
        }

        $app->viewState()->clearFormState();
        $this->flash($app, 'success', 'Service updated successfully.');
        return $this->redirect($app, $app->url()->route('admin.services.index'));
    }

    public function destroy(object $app, array $params = []): Response
    {
        $id = (int)($params['id'] ?? 0);

        if (!$app->csrf()->validate((string)$app->request()->input('_token', ''))) {
            $this->flash($app, 'danger', 'Invalid CSRF token.');
            return $this->redirect($app, $app->url()->route('admin.services.index'));
        }

        /** @var ServiceCrudService $service */
        $service = $app->service('serviceCrud');
        $row = $service->find($id);

        if (!$row) {
            $this->flash($app, 'warning', 'Service not found.');
            return $this->redirect($app, $app->url()->route('admin.services.index'));
        }

        $service->delete($id);
        $this->flash($app, 'success', 'Service deleted successfully.');
        return $this->redirect($app, $app->url()->route('admin.services.index'));
    }
}
