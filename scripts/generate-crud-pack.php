<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

$flags = array_values(array_filter(array_slice($argv, 1), static fn (string $arg): bool => str_starts_with($arg, '--')));
$args = array_values(array_filter(array_slice($argv, 1), static fn (string $arg): bool => !str_starts_with($arg, '--')));
$legacyMode = in_array('--legacy', $flags, true);
$twigMode = in_array('--twig', $flags, true);
$twigOnlyMode = in_array('--twig-only', $flags, true);

if (count($args) < 1) {
    echo "Usage: php scripts/generate-crud-pack.php <blueprint-json-path> [output-dir] [--legacy] [--twig] [--twig-only]\n";
    exit(1);
}

$blueprintPath = $args[0];
$outputDir = $args[1] ?? (BASE_PATH . '/generated/scaffold_output');

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

if ($twigOnlyMode) {
    $data['view_engines'] = ['twig'];
} elseif ($twigMode) {
    $existing = $data['view_engines'] ?? ($data['view_engine'] ?? ['php']);
    if (is_string($existing)) {
        $existing = [$existing];
    }
    $existing[] = 'twig';
    $data['view_engines'] = array_values(array_unique(array_filter(array_map(
        static fn (mixed $engine): string => is_string($engine) ? strtolower(trim($engine)) : '',
        (array)$existing
    ))));
}

if ($legacyMode) {
    require_once BASE_PATH . '/app/Generators/ScaffoldWriter.php';
    $writer = new ScaffoldWriter();
} else {
    require_once BASE_PATH . '/src/Generators/CrudScaffoldWriter.php';
    $writer = new \Cabnet\Generators\CrudScaffoldWriter();
}

$files = $writer->buildCrudPack($data);

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

if ($legacyMode) {
    echo "Generation target: legacy app/\n";
    exit(0);
}

$viewEngines = $data['view_engines'] ?? ($data['view_engine'] ?? ['php']);
if (is_string($viewEngines)) {
    $viewEngines = [$viewEngines];
}
$viewEngines = array_values(array_unique(array_filter(array_map(
    static fn (mixed $engine): string => is_string($engine) ? strtolower(trim($engine)) : '',
    (array)$viewEngines
))));
if ($viewEngines === []) {
    $viewEngines = ['php'];
}

echo 'Generation target: src-first (' . implode(', ', $viewEngines) . ")\n";
