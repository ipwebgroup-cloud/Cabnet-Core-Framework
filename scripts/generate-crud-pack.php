<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/bootstrap/autoload.php';

$flags = array_values(array_filter(array_slice($argv, 1), static fn (string $arg): bool => str_starts_with($arg, '--')));
$args = array_values(array_filter(array_slice($argv, 1), static fn (string $arg): bool => !str_starts_with($arg, '--')));
$legacyMode = in_array('--legacy', $flags, true);
$listExamples = in_array('--list-examples', $flags, true);

if ($listExamples) {
    $examples = \Cabnet\Generators\BlueprintLibrary::listExamples(BASE_PATH);
    if ($examples === []) {
        echo "No built-in blueprint examples were found.
";
        exit(0);
    }

    echo "Built-in blueprint examples:
";
    foreach ($examples as $slug => $meta) {
        $summary = (string)($meta['example_summary'] ?? '');
        $entityKey = (string)($meta['entity_key'] ?? '');
        echo sprintf("- %s", $slug);
        if ($entityKey !== '') {
            echo sprintf(" (entity: %s)", $entityKey);
        }
        if ($summary !== '') {
            echo sprintf(": %s", $summary);
        }
        echo "
";
    }
    exit(0);
}

if (count($args) < 1) {
    echo "Usage: php scripts/generate-crud-pack.php <blueprint-json-path|example:name> [output-dir] [--legacy] [--list-examples]
";
    exit(1);
}

$blueprintReference = $args[0];
$outputDir = $args[1] ?? (BASE_PATH . '/generated/scaffold_output');

try {
    $data = \Cabnet\Generators\BlueprintLibrary::load(BASE_PATH, $blueprintReference);
    \Cabnet\Generators\BlueprintValidator::assertValid($data);
} catch (\Throwable $e) {
    echo $e->getMessage() . "
";
    exit(1);
}

if ($legacyMode) {
    require_once BASE_PATH . '/app/Generators/ScaffoldWriter.php';
    $writer = new \ScaffoldWriter();
} else {
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
    echo "Wrote: {$relative}
";
}

echo 'Done. Output directory: ' . $outputDir . PHP_EOL;
echo $legacyMode
    ? "Generation target: legacy app/
"
    : "Generation target: src-first (views resolve from layered presentation packages).
";
