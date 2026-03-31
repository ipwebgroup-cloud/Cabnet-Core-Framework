<?php
declare(strict_types=1);

if (!($definition ?? null) instanceof CrudEntityDefinition) {
    throw new RuntimeException('CRUD form page requires a CrudEntityDefinition.');
}

$mode = (string)($mode ?? 'create');
$titleText = $mode === 'edit'
    ? 'Edit ' . rtrim($definition->label(), 's')
    : 'Create ' . rtrim($definition->label(), 's');

$submitText = $mode === 'edit'
    ? 'Update ' . rtrim($definition->label(), 's')
    : 'Create ' . rtrim($definition->label(), 's');

$formAction = (string)($formAction ?? '#');
$backPath = (string)($backPath ?? '/');
$csrfToken = (string)($csrfToken ?? '');
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 mb-1"><?= htmlspecialchars($titleText, ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="text-secondary mb-0">Generic CRUD form renderer.</p>
            </div>
            <a href="<?= htmlspecialchars($backPath, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-secondary">Back</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="post" action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>" class="row g-3">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <?php include BASE_PATH . '/src/Presentation/Views/php/admin/crud/form_fields.php'; ?>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark"><?= htmlspecialchars($submitText, ENT_QUOTES, 'UTF-8') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$content = (string)ob_get_clean();
$title = $titleText;
include BASE_PATH . '/src/Presentation/Views/php/layouts/admin.php';
