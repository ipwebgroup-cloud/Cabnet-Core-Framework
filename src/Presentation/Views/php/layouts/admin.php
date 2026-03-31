<?php
declare(strict_types=1);

$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
$currentPath = parse_url($currentPath, PHP_URL_PATH) ?: '/';
$menuItems = [];
if (is_file(BASE_PATH . '/config/admin_menu.php')) {
    $loaded = require BASE_PATH . '/config/admin_menu.php';
    $menuItems = is_array($loaded) ? $loaded : [];
}
$isAuthenticated = isset($authUser) && is_array($authUser) && !empty($authUser);
$logoutAction = (string)($logoutAction ?? '/logout');
$logoutCsrfToken = (string)($logoutCsrfToken ?? '');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Cabnet Core Admin', ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .admin-shell { min-height: 100vh; }
        .admin-sidebar { width: 260px; min-height: 100vh; }
        .admin-main { min-width: 0; }
        .nav-link-button {
            background: none;
            border: 0;
            padding: 0;
            text-align: left;
            width: 100%;
        }
        @media (max-width: 991.98px) {
            .admin-sidebar { width: 100%; min-height: auto; }
        }
    </style>
</head>
<body class="bg-body-tertiary">
<div class="d-lg-flex admin-shell">
    <aside class="admin-sidebar bg-dark text-white p-3">
        <div class="mb-4">
            <div class="fs-5 fw-semibold">Cabnet Core Admin</div>
            <div class="small text-white-50">v3.2 shared layout convergence</div>
        </div>

        <?php if ($isAuthenticated): ?>
            <nav class="nav flex-column gap-1">
                <?php foreach ($menuItems as $item): ?>
                    <?php
                    $requiresAuth = (bool)($item['requires_auth'] ?? false);
                    if ($requiresAuth && !$isAuthenticated) {
                        continue;
                    }

                    $path = (string)($item['path'] ?? '#');
                    $match = (string)($item['match'] ?? $path);
                    $method = strtoupper((string)($item['method'] ?? 'GET'));
                    $active = $match === '/' ? ($currentPath === '/') : str_starts_with($currentPath, $match);
                    $label = htmlspecialchars((string)($item['label'] ?? 'Link'), ENT_QUOTES, 'UTF-8');
                    ?>
                    <?php if ($method === 'POST'): ?>
                        <form method="post" action="<?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?>" class="m-0">
                            <input type="hidden" name="_token" value="<?= htmlspecialchars($logoutCsrfToken, ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="nav-link nav-link-button text-white <?= $active ? 'fw-bold' : '' ?>"><?= $label ?></button>
                        </form>
                    <?php else: ?>
                        <a class="nav-link text-white <?= $active ? 'fw-bold' : '' ?>" href="<?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?>">
                            <?= $label ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
        <?php else: ?>
            <div class="small text-white-50">Sign in with an admin account to access the admin area.</div>
        <?php endif; ?>
    </aside>

    <main class="admin-main flex-grow-1">
        <div class="border-bottom bg-white px-4 py-3">
            <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <div>
                    <div class="fw-semibold"><?= htmlspecialchars($title ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="small text-secondary">Src-owned admin shell foundation</div>
                </div>
                <?php if ($isAuthenticated): ?>
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-end">
                            <div class="small text-secondary">Signed in as</div>
                            <div class="fw-semibold"><?= htmlspecialchars((string)($authUser['name'] ?? $authUser['username'] ?? 'Administrator'), ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                        <form method="post" action="<?= htmlspecialchars($logoutAction, ENT_QUOTES, 'UTF-8') ?>" class="m-0">
                            <input type="hidden" name="_token" value="<?= htmlspecialchars($logoutCsrfToken, ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="btn btn-outline-dark btn-sm">Sign Out</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="p-4">
            <?php include BASE_PATH . '/src/Presentation/Views/php/partials/flash.php'; ?>
            <?= $content ?? '' ?>
        </div>
    </main>
</div>
</body>
</html>
