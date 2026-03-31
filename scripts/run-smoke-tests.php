<?php

declare(strict_types=1);

ob_start();

require_once __DIR__ . '/../tests/bootstrap.php';

$runner = new \Tests\Smoke\FrameworkSmokeTest();
$results = $runner->run();
$passed = array_values(array_filter($results, static fn (array $result): bool => $result['passed']));
$failed = array_values(array_filter($results, static fn (array $result): bool => !$result['passed']));

ob_end_clean();

fwrite(STDOUT, "Cabnet Core Smoke Tests\n");
fwrite(STDOUT, "========================\n");

foreach ($results as $result) {
    $status = $result['passed'] ? 'PASS' : 'FAIL';
    fwrite(STDOUT, sprintf("[%s] %s", $status, $result['name']));
    if (!$result['passed']) {
        fwrite(STDOUT, ' :: ' . $result['detail']);
    }
    fwrite(STDOUT, "\n");
}

fwrite(STDOUT, "------------------------\n");
fwrite(STDOUT, 'Passed: ' . count($passed) . "\n");
fwrite(STDOUT, 'Failed: ' . count($failed) . "\n");

exit(count($failed) === 0 ? 0 : 1);
