<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

$flags = array_values(array_filter(array_slice($argv, 1), static fn (string $arg): bool => str_starts_with($arg, '--')));
$args = array_values(array_filter(array_slice($argv, 1), static fn (string $arg): bool => !str_starts_with($arg, '--')));
$legacyMode = in_array('--legacy', $flags, true);

if (count($args) < 4) {
    echo "Usage: php scripts/generate-entity.php <entity_key> <singular_label> <plural_label> <table> [--legacy]\n";
    exit(1);
}

if ($legacyMode) {
    require_once BASE_PATH . '/app/Generators/EntityGenerator.php';
    $generator = new EntityGenerator();
} else {
    require_once BASE_PATH . '/src/Generators/EntityGenerator.php';
    $generator = new \Cabnet\Generators\EntityGenerator();
}

[$entityKey, $singularLabel, $pluralLabel, $table] = $args;
$result = $generator->generate($entityKey, $singularLabel, $pluralLabel, $table);

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
