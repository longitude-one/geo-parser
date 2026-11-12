<?php

/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2024-2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * Copyright (c) 2014-2019 Derek J. Lambert (creof/geo-parser)
 *
 * See LICENSE for copyright and license information.
 *
 */

declare(strict_types=1);



// Replace the value of this variable with the project's launch year.
$firstYear = 2024;

function __copyright(int $launchYear): string
{
    $currentYear = (int) date('Y');
    if ($currentYear === $launchYear) {
        return (string) $currentYear;
    }

    return "$launchYear-$currentYear";
}

$header = file_get_contents(__DIR__.'/headers.txt');
$header = str_replace('%year%', __copyright($firstYear), $header);

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/../../lib/',
        __DIR__.'/../../tests/',
        __DIR__.'/../../quality/',
    ])
    ->append([
        __FILE__,
    ])
;

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@Symfony' => true,
    '@Symfony:risky' => true,
    '@autoPHPMigration' => true,
    '@autoPHPMigration:risky' => true,
    '@autoPHPUnitMigration:risky' => true,
    'declare_strict_types' => true,
    // Keep parentheses for PHP 8.3 compatibility.
    'new_expression_parentheses' => [
        'use_parentheses' => true,
    ],
    'ordered_interfaces' => [
        'direction' => 'ascend',
        'order' => 'alpha',
    ],
    'php_unit_test_case_static_method_calls' => [
        'call_type' => 'self',
        'target' => 'newest',
    ],
    'header_comment' => [
        'header' => $header,
        'comment_type' => 'comment',
        'location' => 'after_open',
        'separate' => 'bottom',
    ],
    'ordered_class_elements' => [
        'order' => [
            'use_trait',
            'case',
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
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/.php_cs.cache')
;
