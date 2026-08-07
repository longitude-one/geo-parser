<?php

/**
 * This file is part of the LongitudeOne GeoParser project.
 *
 * PHP 8.3 | 8.4 | 8.5
 *
 * Copyright LongitudeOne - Alexandre Tranchant - Derek J. Lambert.
 * Copyright 2024-2026.
 */

namespace LongitudeOne\Geo\String\Tests;

use LongitudeOne\Geo\String\Parser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Regression tests for fractional coordinate components.
 *
 * These values are deliberately sensitive to floating-point normalization.
 */
class FloatTest extends TestCase
{
    /**
     * @return \Generator<string, array{string, float|array<int, float>}, null, void>
     */
    public static function dataSourceFractionalMinutes(): \Generator
    {
        yield 'zero decimal minutes' => ["55°0.0'", 55.0];
        yield 'integer minutes' => ["55°17'", 55.28333333333333];
        yield 'decimal minutes' => ["55°17.60'", 55.29333333333333];
        yield 'decimal minutes below sixty' => ["55°59.9999999999999'", 56.0];
        yield 'decimal minutes in a coordinate pair' => ["40°26.222' 79°58.52'", [40.43703333333333, 79.97533333333332]];
    }

    /**
     * @return \Generator<string, array{string, float|array<int, float>}, null, void>
     */
    public static function dataSourceFractionalSeconds(): \Generator
    {
        yield 'zero decimal seconds' => ["55°17'0.0\"", 55.28333333333333];
        yield 'integer seconds' => ["55°17'25\"", 55.29027777777778];
        yield 'fractional seconds' => ["+93° 19' 25.8\"", 93.32383333333333];
        yield 'fractional seconds below sixty' => ["55°17'59.9999999999999\"", 55.3];
        yield 'fractional seconds in a coordinate pair' => ["44°58'53.9\"N 93°19'25.7\"W", [44.98163888888889, -93.32380555555557]];
    }

    /** @param float|array<int, float> $expected */
    #[DataProvider('dataSourceFractionalMinutes')]
    public function testFractionalMinutesAreNormalized(string $input, float|array $expected): void
    {
        self::assertSame($expected, (new Parser($input))->parse());
    }

    /** @param float|array<int, float> $expected */
    #[DataProvider('dataSourceFractionalSeconds')]
    public function testFractionalSecondsAreNormalized(string $input, float|array $expected): void
    {
        self::assertSame($expected, (new Parser($input))->parse());
    }
}
