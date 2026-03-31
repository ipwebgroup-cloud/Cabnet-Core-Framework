<?php
declare(strict_types=1);

$definition = $definition ?? null;
$old = is_array($old ?? null) ? $old : [];
$errors = is_array($errors ?? null) ? $errors : [];
$row = is_array($row ?? null) ? $row : [];
$values = !empty($old) ? $old : $row;

if (!$definition instanceof CrudEntityDefinition) {
    throw new RuntimeException('CRUD form fields require a CrudEntityDefinition.');
}

$fieldError = static function(array $errors, string $field, ?string $locale = null): ?string {
    $key = $locale !== null ? $field . '.' . $locale : $field;
    return $errors[$key][0] ?? null;
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
    $isTranslatable = !empty($meta['translatable']);
    $isUpload = $definition->isUploadFieldName($name);
?>
    <div class="col-12">
        <label class="form-label"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?><?= $required ? ' *' : '' ?></label>

        <?php if ($isTranslatable): ?>
            <?php
            $locales = $definition->localesForField($name);
            $localeValues = is_array($value) ? $value : [];
            ?>
            <div class="row g-2">
                <?php foreach ($locales as $locale): ?>
                    <?php
                    $localeValue = (string)($localeValues[$locale] ?? '');
                    $localeError = $fieldError($errors, $name, $locale);
                    ?>
                    <div class="<?= $type === 'textarea' ? 'col-12' : 'col-md-6' ?>">
                        <label class="form-label small text-uppercase text-secondary"><?= htmlspecialchars($locale, ENT_QUOTES, 'UTF-8') ?></label>
                        <?php if ($type === 'textarea'): ?>
                            <textarea
                                name="<?= htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8') ?>[<?= htmlspecialchars((string)$locale, ENT_QUOTES, 'UTF-8') ?>]"
                                rows="<?= (int)$rows ?>"
                                class="form-control <?= $localeError ? 'is-invalid' : '' ?>"
                                <?= $required ? 'required' : '' ?>
                                <?= $max !== null ? 'maxlength="' . (int)$max . '"' : '' ?>
                                <?= $placeholder !== '' ? 'placeholder="' . htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
                            ><?= htmlspecialchars($localeValue, ENT_QUOTES, 'UTF-8') ?></textarea>
                        <?php else: ?>
                            <input
                                type="<?= htmlspecialchars($inputType, ENT_QUOTES, 'UTF-8') ?>"
                                name="<?= htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8') ?>[<?= htmlspecialchars((string)$locale, ENT_QUOTES, 'UTF-8') ?>]"
                                class="form-control <?= $localeError ? 'is-invalid' : '' ?>"
                                value="<?= htmlspecialchars($localeValue, ENT_QUOTES, 'UTF-8') ?>"
                                <?= $required ? 'required' : '' ?>
                                <?= $min !== null && $inputType !== 'number' ? 'minlength="' . (int)$min . '"' : '' ?>
                                <?= $max !== null && $inputType !== 'number' ? 'maxlength="' . (int)$max . '"' : '' ?>
                                <?= $placeholder !== '' ? 'placeholder="' . htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
                            >
                        <?php endif; ?>

                        <?php if ($localeError): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars((string)$localeError, ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php elseif ($isUpload): ?>
            <?php
            $accept = (string)($meta['accept'] ?? ($type === 'image' || !empty($meta['image']) ? 'image/*' : ''));
            $currentPath = is_string($value) ? $value : (is_string($row[$name] ?? null) ? (string)$row[$name] : '');
            ?>
            <input
                type="file"
                name="<?= htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8') ?>"
                class="form-control <?= $error ? 'is-invalid' : '' ?>"
                <?= $accept !== '' ? 'accept="' . htmlspecialchars($accept, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
                <?= $required && $currentPath === '' ? 'required' : '' ?>
            >
            <?php if ($currentPath !== ''): ?>
                <div class="form-text">Current file: <a href="<?= htmlspecialchars($currentPath, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars(basename($currentPath), ENT_QUOTES, 'UTF-8') ?></a></div>
            <?php endif; ?>

        <?php elseif ($type === 'textarea'): ?>
            <textarea
                name="<?= htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8') ?>"
                rows="<?= (int)$rows ?>"
                class="form-control <?= $error ? 'is-invalid' : '' ?>"
                <?= $required ? 'required' : '' ?>
                <?= $max !== null ? 'maxlength="' . (int)$max . '"' : '' ?>
                <?= $placeholder !== '' ? 'placeholder="' . htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
            ><?= htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') ?></textarea>

        <?php elseif ($type === 'select'): ?>
            <?php $options = is_array($meta['options'] ?? null) ? $meta['options'] : []; ?>
            <select
                name="<?= htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8') ?>"
                class="form-select <?= $error ? 'is-invalid' : '' ?>"
                <?= $required ? 'required' : '' ?>
            >
                <?php if (!$required || $placeholder !== ''): ?>
                    <option value=""><?= htmlspecialchars($placeholder !== '' ? $placeholder : 'Select an option', ENT_QUOTES, 'UTF-8') ?></option>
                <?php endif; ?>
                <?php foreach ($options as $optionValue => $optionLabel): ?>
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
            <div class="invalid-feedback d-block"><?= htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
