<?php
$definition = $definition ?? null;
$old = is_array($old ?? null) ? $old : [];
$errors = is_array($errors ?? null) ? $errors : [];
$row = is_array($row ?? null) ? $row : [];
$values = !empty($old) ? $old : $row;

if (!$definition instanceof CrudEntityDefinition) {
    throw new RuntimeException('CRUD form fields require a CrudEntityDefinition.');
}

$fieldError = static function(array $errors, string $field): ?string {
    return $errors[$field][0] ?? null;
};

foreach ($definition->fields() as $name => $meta):
    $type = (string)($meta['type'] ?? 'text');
    $label = (string)($meta['label'] ?? ucfirst($name));
    $required = !empty($meta['required']);
    $value = $values[$name] ?? '';
    $error = $fieldError($errors, $name);
    $placeholder = (string)($meta['placeholder'] ?? '');
    $help = (string)($meta['help'] ?? '');
    $rows = max(2, (int)($meta['rows'] ?? 5));
    $min = isset($meta['min']) ? (int)$meta['min'] : null;
    $max = isset($meta['max']) ? (int)$meta['max'] : null;
    $inputType = match ($type) {
        'email' => 'email',
        'integer', 'number' => 'number',
        default => 'text',
    };
?>
    <div class="<?= $type === 'textarea' ? 'col-12' : 'col-md-6' ?>">
        <label class="form-label"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?><?= $required ? ' *' : '' ?></label>

        <?php if ($type === 'textarea'): ?>
            <textarea
                name="<?= htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8') ?>"
                rows="<?= (int)$rows ?>"
                class="form-control <?= $error ? 'is-invalid' : '' ?>"
                <?= $required ? 'required' : '' ?>
                <?= $max !== null ? 'maxlength="' . (int)$max . '"' : '' ?>
                <?= $placeholder !== '' ? 'placeholder="' . htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
            ><?= htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') ?></textarea>

        <?php elseif ($type === 'select'): ?>
            <select
                name="<?= htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8') ?>"
                class="form-select <?= $error ? 'is-invalid' : '' ?>"
                <?= $required ? 'required' : '' ?>
            >
                <?php foreach (($meta['options'] ?? []) as $optionValue => $optionLabel): ?>
                    <option
                        value="<?= htmlspecialchars((string)$optionValue, ENT_QUOTES, 'UTF-8') ?>"
                        <?= (string)$value === (string)$optionValue ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars((string)$optionLabel, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>

        <?php else: ?>
            <input
                type="<?= htmlspecialchars($inputType, ENT_QUOTES, 'UTF-8') ?>"
                name="<?= htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8') ?>"
                class="form-control <?= $error ? 'is-invalid' : '' ?>"
                value="<?= htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') ?>"
                <?= $required ? 'required' : '' ?>
                <?= $min !== null && $inputType === 'number' ? 'min="' . (int)$min . '"' : '' ?>
                <?= $max !== null && $inputType === 'number' ? 'max="' . (int)$max . '"' : '' ?>
                <?= $min !== null && $inputType !== 'number' ? 'minlength="' . (int)$min . '"' : '' ?>
                <?= $max !== null && $inputType !== 'number' ? 'maxlength="' . (int)$max . '"' : '' ?>
                <?= $placeholder !== '' ? 'placeholder="' . htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
            >
        <?php endif; ?>

        <?php if ($help !== ''): ?>
            <div class="form-text"><?= htmlspecialchars($help, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="invalid-feedback"><?= htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
