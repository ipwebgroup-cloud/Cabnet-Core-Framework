<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

$flags = array_values(array_filter(array_slice($argv, 1), static fn (string $arg): bool => str_starts_with($arg, '--')));
$args = array_values(array_filter(array_slice($argv, 1), static fn (string $arg): bool => !str_starts_with($arg, '--')));
$legacyMode = in_array('--legacy', $flags, true);

if (count($args) < 1) {
    echo "Usage: php scripts/generate-integration-patches.php <blueprint-json-path> [output-dir] [--legacy]\n";
    exit(1);
}

$blueprintPath = $args[0];
$outputDir = $args[1] ?? (BASE_PATH . '/generated/integration_output');

if (!is_file($blueprintPath)) {
    echo "Blueprint file not found: {$blueprintPath}\n";
    exit(1);
}

$raw = file_get_contents($blueprintPath);
$data = json_decode((string)$raw, true);

if (!is_array($data)) {
    echo "Invalid blueprint JSON.\n";
    exit(1);
}

if ($legacyMode) {
    require_once BASE_PATH . '/app/Generators/IntegrationPatcher.php';
    $patcher = new IntegrationPatcher();
} else {
    require_once BASE_PATH . '/src/Generators/IntegrationPatcher.php';
    $patcher = new \Cabnet\Generators\IntegrationPatcher();
}

$files = $patcher->buildPatches($data);

foreach ($files as $relative => $content) {
    $full = rtrim($outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    $dir = dirname($full);

    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents($full, $content);
    echo "Wrote: {$relative}\n";
}

echo 'Done. Output directory: ' . $outputDir . PHP_EOL;
echo $legacyMode
    ? "Patch target: legacy app/\n"
    : "Patch target: src-first (views remain under app/Views/php during the rendering bridge).\n";
