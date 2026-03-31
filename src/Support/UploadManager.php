<?php
declare(strict_types=1);

namespace Cabnet\Support;

use Cabnet\Application\Crud\CrudEntityDefinition;
use RuntimeException;

final class UploadManager
{
    /** @param array<string, mixed> $config */
    public function __construct(private array $config = [])
    {
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed>|null $existing
     * @return array<string, mixed>
     */
    public function persistConfiguredUploads(CrudEntityDefinition $definition, array $data, ?array $existing = null): array
    {
        foreach ($definition->fields() as $field => $meta) {
            if (!$definition->isUploadFieldName($field)) {
                continue;
            }

            $value = $data[$field] ?? null;

            if ($this->isUploadedFile($value)) {
                $data[$field] = $this->storeUploadedFile($definition, $field, $value, $meta);
                continue;
            }

            if (($value === null || $value === '') && is_array($existing) && array_key_exists($field, $existing)) {
                $data[$field] = $existing[$field];
                continue;
            }

            if (!is_string($value) || $value === '') {
                $data[$field] = null;
            }
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $file
     * @param array<string, mixed> $meta
     */
    public function storeUploadedFile(CrudEntityDefinition $definition, string $field, array $file, array $meta = []): string
    {
        $tmpName = (string)($file['tmp_name'] ?? '');
        $originalName = (string)($file['name'] ?? 'upload.bin');

        if ($tmpName === '') {
            throw new RuntimeException("Upload field [{$field}] is missing a temporary file.");
        }

        $directory = $this->targetDirectory($definition, $field, $meta);
        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new RuntimeException("Failed to create upload directory [{$directory}].");
        }

        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $base = pathinfo($originalName, PATHINFO_FILENAME);
        $base = $this->sanitizeFileName($base !== '' ? $base : $field);
        $filename = $base . '-' . date('YmdHis') . '-' . substr(bin2hex(random_bytes(4)), 0, 8);
        if ($extension !== '') {
            $filename .= '.' . strtolower($extension);
        }

        $destination = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . $filename;
        $this->moveFile($tmpName, $destination);

        $publicPrefix = rtrim((string)($this->config['public_uploads_url'] ?? '/assets/uploads'), '/');
        $subdir = trim((string)($meta['upload_dir'] ?? $definition->key()), '/');

        return $publicPrefix . '/' . ($subdir !== '' ? $subdir . '/' : '') . $filename;
    }

    private function targetDirectory(CrudEntityDefinition $definition, string $field, array $meta): string
    {
        $base = rtrim((string)($this->config['public_uploads_path'] ?? BASE_PATH . '/public/assets/uploads'), '/\\');
        $subdir = trim((string)($meta['upload_dir'] ?? $definition->key()), '/');

        return $base . ($subdir !== '' ? DIRECTORY_SEPARATOR . $subdir : '');
    }

    /** @param array<string, mixed>|null $file */
    private function isUploadedFile(?array $file): bool
    {
        if (!is_array($file)) {
            return false;
        }

        $tmpName = (string)($file['tmp_name'] ?? '');
        $error = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);

        return $tmpName !== '' && $error === UPLOAD_ERR_OK;
    }

    private function moveFile(string $source, string $destination): void
    {
        if (function_exists('move_uploaded_file') && @move_uploaded_file($source, $destination)) {
            return;
        }

        if (@rename($source, $destination)) {
            return;
        }

        if (@copy($source, $destination)) {
            @unlink($source);
            return;
        }

        throw new RuntimeException("Failed to move uploaded file [{$source}] to [{$destination}].");
    }

    private function sanitizeFileName(string $value): string
    {
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9\-_]+/', '-', $value) ?? 'file';
        $value = trim($value, '-_');

        return $value !== '' ? $value : 'file';
    }
}
