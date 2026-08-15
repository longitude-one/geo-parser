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

namespace LongitudeOne\Geo\String\Tests;

use LongitudeOne\Geo\String\Internal\Angle;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Angle conversion tests.
 */
class AngleTest extends TestCase
{
    /**
     * Provide angle components and their decimal-degree values.
     *
     * @return \Generator<string, array{int|float, float|null, float|null, int|float}, null, void>
     */
    public static function dataSourceAngles(): \Generator
    {
        yield 'integer degrees' => [40, null, null, 40];
        yield 'decimal degrees' => [40.0, null, null, 40.0];
        yield 'degrees and minutes' => [40, 30.0, null, 40.5];
        yield 'decimal minutes' => [55, 17.6, null, 55.29333333333333];
        yield 'degrees, minutes, and seconds' => [55, 17.0, 25.0, 55.29027777777778];
        yield 'fractional seconds' => [93, 19.0, 25.8, 93.32383333333333];
    }

    /**
     * Angle components must preserve the parser's current decimal conversion
     * and floating-point normalization.
     *
     * @param int|float  $degrees  degree component
     * @param float|null $minutes  minute component
     * @param float|null $seconds  second component
     * @param int|float  $expected expected decimal degrees
     */
    #[DataProvider('dataSourceAngles')]
    public function testConvertsToDecimalDegrees(int|float $degrees, ?float $minutes, ?float $seconds, int|float $expected): void
    {
        self::assertSame($expected, (new Angle($degrees, $minutes, $seconds))->toDecimalDegrees());
    }
}
