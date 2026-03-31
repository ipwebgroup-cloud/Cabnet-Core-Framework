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
    $type = $meta['type'] ?? 'text';
    $label = $meta['label'] ?? ucfirst($name);
    $required = !empty($meta['required']);
    $value = $values[$name] ?? '';
    $error = $fieldError($errors, $name);
?>
    <div class="<?= $type === 'textarea' ? 'col-12' : 'col-md-6' ?>">
        <label class="form-label"><?= htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8') ?><?= $required ? ' *' : '' ?></label>

        <?php if ($type === 'textarea'): ?>
            <textarea
                name="<?= htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8') ?>"
                rows="5"
                class="form-control <?= $error ? 'is-invalid' : '' ?>"
            ><?= htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') ?></textarea>

        <?php elseif ($type === 'select'): ?>
            <select
                name="<?= htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8') ?>"
                class="form-select <?= $error ? 'is-invalid' : '' ?>"
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
                type="text"
                name="<?= htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8') ?>"
                class="form-control <?= $error ? 'is-invalid' : '' ?>"
                value="<?= htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') ?>"
            >
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="invalid-feedback"><?= htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
