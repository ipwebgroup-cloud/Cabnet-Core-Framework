<?php ob_start(); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h3 mb-3"><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="text-secondary">Public starter controller, session layer, flash system, and the shared src presentation shell are working.</p>
                <dl class="row mb-0">
                    <dt class="col-sm-3">Context</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($context, ENT_QUOTES, 'UTF-8') ?></dd>
                    <dt class="col-sm-3">Timestamp</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($now, ENT_QUOTES, 'UTF-8') ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>
<?php
$content = (string)ob_get_clean();
$title = $appName;
include BASE_PATH . '/src/Presentation/Views/php/layouts/public.php';
