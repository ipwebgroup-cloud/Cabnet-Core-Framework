<?php
declare(strict_types=1);

if (!($definition ?? null) instanceof CrudEntityDefinition) {
    throw new RuntimeException('CRUD index table requires a CrudEntityDefinition.');
}

$rows = is_array($rows ?? null) ? $rows : [];
$csrfToken = (string)($csrfToken ?? '');
$listPath = (string)($listPath ?? '/');
$createPath = (string)($createPath ?? $listPath . '/create');
$editRouteName = (string)($editRouteName ?? '');
$deleteRouteName = (string)($deleteRouteName ?? '');
$search = (string)($search ?? '');
$paginator = $paginator ?? null;
$urlService = $urlService ?? null;
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <div>
        <h1 class="h3 mb-1"><?= htmlspecialchars($definition->label(), ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="text-secondary mb-0">Reusable CRUD-ready list view with search and pagination.</p>
    </div>
    <a href="<?= htmlspecialchars($createPath, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-dark">New <?= htmlspecialchars(rtrim($definition->label(), 's'), ENT_QUOTES, 'UTF-8') ?></a>
</div>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <form method="get" action="<?= htmlspecialchars($listPath, ENT_QUOTES, 'UTF-8') ?>" class="row g-2 align-items-end">
            <div class="col-md-6 col-lg-4">
                <label class="form-label">Search</label>
                <input type="text" name="q" class="form-control" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Search...">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-dark">Apply</button>
            </div>
            <div class="col-auto">
                <a href="<?= htmlspecialchars($listPath, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <?php foreach ($definition->listColumns() as $column): ?>
                        <th><?= htmlspecialchars(ucfirst($column), ENT_QUOTES, 'UTF-8') ?></th>
                    <?php endforeach; ?>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($rows)): ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <?php foreach ($definition->listColumns() as $column): ?>
                            <td><?= htmlspecialchars((string)($row[$column] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <?php endforeach; ?>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="<?= htmlspecialchars($urlService->route($editRouteName, ['id' => (int)($row['id'] ?? 0)]), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form method="post" action="<?= htmlspecialchars($urlService->route($deleteRouteName, ['id' => (int)($row['id'] ?? 0)]), ENT_QUOTES, 'UTF-8') ?>" onsubmit="return confirm('Delete this item?');">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="<?= count($definition->listColumns()) + 1 ?>" class="text-secondary">No records found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (($paginator ?? null) instanceof Paginator && $paginator->hasPages()): ?>
    <nav class="mt-3">
        <ul class="pagination mb-0">
            <li class="page-item <?= $paginator->page() <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= htmlspecialchars($listPath . '?' . http_build_query(['q' => $search, 'page' => $paginator->previousPage()]), ENT_QUOTES, 'UTF-8') ?>">Previous</a>
            </li>
            <?php foreach ($paginator->pageRange() as $pageNumber): ?>
                <li class="page-item <?= $pageNumber === $paginator->page() ? 'active' : '' ?>">
                    <a class="page-link" href="<?= htmlspecialchars($listPath . '?' . http_build_query(['q' => $search, 'page' => $pageNumber]), ENT_QUOTES, 'UTF-8') ?>">
                        <?= (int)$pageNumber ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <li class="page-item <?= $paginator->page() >= $paginator->pages() ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= htmlspecialchars($listPath . '?' . http_build_query(['q' => $search, 'page' => $paginator->nextPage()]), ENT_QUOTES, 'UTF-8') ?>">Next</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>
