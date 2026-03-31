<?php
declare(strict_types=1);
?>
<?php if (!empty($flashMessages ?? [])): ?>
    <?php foreach ($flashMessages as $type => $messages): ?>
        <?php foreach ((array)$messages as $message): ?>
            <div class="alert alert-<?= htmlspecialchars((string)$type, ENT_QUOTES, 'UTF-8') ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars((string)$message, ENT_QUOTES, 'UTF-8') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
