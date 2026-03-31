<?php ob_start(); ?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Admin Sign In</h1>
                <form method="post" action="/login" class="d-grid gap-3">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars((string)($csrfToken ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <div>
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="" autocomplete="username" required>
                    </div>
                    <div>
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" value="" autocomplete="current-password" required>
                    </div>
                    <button type="submit" class="btn btn-dark">Sign In</button>
                </form>
                <p class="small text-secondary mt-3 mb-0">Use a configured admin account. For local setup, create one with <code>php scripts/create-admin-user.php</code>.</p>
            </div>
        </div>
    </div>
</div>
<?php
$content = (string)ob_get_clean();
$title = ($appName ?? 'Cabnet Core') . ' Login';
include BASE_PATH . '/app/Views/php/layouts/admin.php';
