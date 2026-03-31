<?php
declare(strict_types=1);

final class ProductController extends BaseCrudController
{
    protected function entityDefinition(): CrudEntityDefinition
    {
        return ProductEntityDefinition::make();
    }

    public function index(App $app, array $params = []): Response
    {
        /** @var ProductCrudService $service */
        $service = $app->service('productCrud');
        $search = trim((string)$app->request()->query('q', ''));
        $page = (int)$app->request()->query('page', 1);

        $pageData = $service->paginate($search, $page, 10);

        return $this->render($app, 'admin/products/index.php', $this->listViewData(
            $app,
            $pageData,
            $search,
            'admin.products'
        ));
    }

    public function createForm(App $app, array $params = []): Response
    {
        return $this->render($app, 'admin/products/create.php', $this->formViewData(
            $app,
            'create',
            $app->url()->route('admin.products.store'),
            $app->url()->route('admin.products.index')
        ));
    }

    public function store(App $app, array $params = []): Response
    {
        if (!$app->csrf()->validate((string)$app->request()->input('_token', ''))) {
            $this->flash($app, 'danger', 'Invalid CSRF token.');
            return $this->redirect($app, $app->url()->route('admin.products.create'));
        }

        /** @var ProductCrudService $service */
        $service = $app->service('productCrud');

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
            return $this->redirect($app, $app->url()->route('admin.products.create'));
        }

        $app->viewState()->clearFormState();
        $this->flash($app, 'success', 'Product created successfully.');
        return $this->redirect($app, $app->url()->route('admin.products.index'));
    }

    public function editForm(App $app, array $params = []): Response
    {
        $id = (int)($params['id'] ?? 0);

        /** @var ProductCrudService $service */
        $service = $app->service('productCrud');
        $row = $service->find($id);

        if (!$row) {
            $this->flash($app, 'warning', 'Product not found.');
            return $this->redirect($app, $app->url()->route('admin.products.index'));
        }

        return $this->render($app, 'admin/products/edit.php', $this->formViewData(
            $app,
            'edit',
            $app->url()->route('admin.products.update', ['id' => $id]),
            $app->url()->route('admin.products.index'),
            $row
        ));
    }

    public function update(App $app, array $params = []): Response
    {
        $id = (int)($params['id'] ?? 0);

        if (!$app->csrf()->validate((string)$app->request()->input('_token', ''))) {
            $this->flash($app, 'danger', 'Invalid CSRF token.');
            return $this->redirect($app, $app->url()->route('admin.products.edit', ['id' => $id]));
        }

        /** @var ProductCrudService $service */
        $service = $app->service('productCrud');
        $row = $service->find($id);

        if (!$row) {
            $this->flash($app, 'warning', 'Product not found.');
            return $this->redirect($app, $app->url()->route('admin.products.index'));
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
            return $this->redirect($app, $app->url()->route('admin.products.edit', ['id' => $id]));
        }

        $app->viewState()->clearFormState();
        $this->flash($app, 'success', 'Product updated successfully.');
        return $this->redirect($app, $app->url()->route('admin.products.index'));
    }

    public function destroy(App $app, array $params = []): Response
    {
        $id = (int)($params['id'] ?? 0);

        if (!$app->csrf()->validate((string)$app->request()->input('_token', ''))) {
            $this->flash($app, 'danger', 'Invalid CSRF token.');
            return $this->redirect($app, $app->url()->route('admin.products.index'));
        }

        /** @var ProductCrudService $service */
        $service = $app->service('productCrud');
        $row = $service->find($id);

        if (!$row) {
            $this->flash($app, 'warning', 'Product not found.');
            return $this->redirect($app, $app->url()->route('admin.products.index'));
        }

        $service->delete($id);
        $this->flash($app, 'success', 'Product deleted successfully.');
        return $this->redirect($app, $app->url()->route('admin.products.index'));
    }
}
