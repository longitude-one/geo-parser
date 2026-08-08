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

// Replace the value of this variable with the project's launch year.
$firstYear = 2024;

function __copyright(int $launchYear): string
{
    $currentYear = (int) date('Y');
    if ($currentYear === $launchYear) {
        return $currentYear;
    }

    return "$launchYear-$currentYear";
}

$header = file_get_contents(__DIR__.'/headers.txt');
$header = str_replace('%year%', __copyright($firstYear), $header);

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/../../lib/',
        __DIR__.'/../../tests/',
    ])
    ->append([
        __FILE__,
    ]);

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@Symfony' => true,
    'declare_strict_types' => true,
    'header_comment' => [
        'header' => $header,
        'comment_type' => 'PHPDoc',
    ],
    'ordered_class_elements' => [
        'order' => [
            'use_trait',
            'constant_public', 'constant_protected', 'constant_private', 'constant',
            'property_public_static', 'property_protected_static', 'property_private_static', 'property_static',
            'property_public', 'property_protected', 'property_private', 'property',
            'construct', 'destruct',
            'phpunit',
            'method_public_static', 'method_protected_static', 'method_private_static', 'method_static',
            'method_public', 'method_protected', 'method_private', 'method', 'magic',
        ],
        'sort_algorithm' => 'alpha',
    ],
])
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/.php_cs.cache')
;
