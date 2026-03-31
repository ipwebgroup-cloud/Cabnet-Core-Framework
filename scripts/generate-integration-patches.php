<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/bootstrap/autoload.php';

$flags = array_values(array_filter(array_slice($argv, 1), static fn (string $arg): bool => str_starts_with($arg, '--')));
$args = array_values(array_filter(array_slice($argv, 1), static fn (string $arg): bool => !str_starts_with($arg, '--')));
$legacyMode = in_array('--legacy', $flags, true);
$listExamples = in_array('--list-examples', $flags, true);

if ($listExamples) {
    printAvailableExamples();
    exit(0);
}

if (count($args) < 1) {
    echo "Usage: php scripts/generate-integration-patches.php <blueprint-json-path|example:name> [output-dir] [--legacy] [--list-examples]\n";
    exit(1);
}

$blueprintInput = $args[0];
$outputDir = $args[1] ?? (BASE_PATH . '/generated/integration_output');
$blueprintPath = \Cabnet\Generators\BlueprintLibrary::resolvePath(BASE_PATH, $blueprintInput);

if ($blueprintPath === null || !is_file($blueprintPath)) {
    echo "Blueprint file not found: {$blueprintInput}\n";
    echo "Run with --list-examples to see built-in blueprint names.\n";
    exit(1);
}

$raw = file_get_contents($blueprintPath);
$data = json_decode((string)$raw, true);

if (!is_array($data)) {
    echo "Invalid blueprint JSON.\n";
    exit(1);
}

$patcher = $legacyMode
    ? new \IntegrationPatcher()
    : new \Cabnet\Generators\IntegrationPatcher();

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

echo 'Blueprint source: ' . $blueprintPath . PHP_EOL;
echo 'Done. Output directory: ' . $outputDir . PHP_EOL;
echo $legacyMode
    ? "Patch target: legacy app/\n"
    : "Patch target: src-first (views remain under app/Views/php during the rendering bridge).\n";

function printAvailableExamples(): void
{
    $examples = \Cabnet\Generators\BlueprintLibrary::listExamples(BASE_PATH);
    if ($examples === []) {
        echo "No built-in examples were found under blueprints/examples.\n";
        return;
    }

    echo "Built-in blueprint examples:\n";
    foreach ($examples as $name => $meta) {
        $engines = implode(', ', $meta['view_engines']);
        $tags = $meta['feature_tags'] === [] ? '' : ' [' . implode(', ', $meta['feature_tags']) . ']';
        echo "- {$name} ({$meta['entity_key']}; views: {$engines}){$tags}\n";
        echo "  {$meta['summary']}\n";
    }
}
