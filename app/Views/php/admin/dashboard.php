<?php ob_start(); ?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h3 mb-3"><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?> Admin</h1>
                <p class="text-secondary">Admin auth flow, CSRF service, validation layer, and first CRUD scaffold are now wired in.</p>
                <dl class="row mb-0">
                    <dt class="col-sm-3">Context</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($context, ENT_QUOTES, 'UTF-8') ?></dd>
                    <dt class="col-sm-3">Timestamp</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($now, ENT_QUOTES, 'UTF-8') ?></dd>
                    <dt class="col-sm-3">Signed in as</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars((string)($user['name'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8') ?></dd>
                </dl>
                <hr>
                <a href="/services" class="btn btn-dark">Open Services CRUD</a>
            </div>
        </div>
    </div>
</div>
<?php
$content = (string)ob_get_clean();
$title = $appName . ' Admin';
include BASE_PATH . '/app/Views/php/layouts/admin.php';
