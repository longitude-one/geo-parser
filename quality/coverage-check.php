<?php

declare(strict_types=1);

/**
 * This file is part of the LongitudeOne GeoParser project.
 *
 * PHP 8.3 | 8.4 | 8.5
 *
 * Copyright LongitudeOne - Alexandre Tranchant - Derek J. Lambert.
 * Copyright 2024-2026.
 */

const MINIMUM_LINE_COVERAGE = 99.0;

$coverageFile = $argv[1] ?? '.phpunit.cache/coverage.xml';

if (!is_file($coverageFile)) {
    fwrite(STDERR, sprintf("Coverage report not found: %s\n", $coverageFile));

    exit(2);
}

$document = new DOMDocument();
if (!$document->load($coverageFile)) {
    fwrite(STDERR, sprintf("Unable to read coverage report: %s\n", $coverageFile));

    exit(2);
}

$metrics = (new DOMXPath($document))->query('/coverage/project/metrics')->item(0);

if (!$metrics instanceof DOMElement) {
    fwrite(STDERR, "Coverage report does not contain project metrics.\n");

    exit(2);
}

$statements = (int) $metrics->getAttribute('statements');
$coveredStatements = (int) $metrics->getAttribute('coveredstatements');

if (0 === $statements) {
    fwrite(STDERR, "Coverage report does not contain executable statements.\n");

    exit(2);
}

$lineCoverage = 100 * $coveredStatements / $statements;

printf("Line coverage: %.2f%% (minimum: %.2f%%)\n", $lineCoverage, MINIMUM_LINE_COVERAGE);

if ($lineCoverage < MINIMUM_LINE_COVERAGE) {
    fwrite(STDERR, "Line coverage is below the required minimum.\n");

    exit(1);
}
