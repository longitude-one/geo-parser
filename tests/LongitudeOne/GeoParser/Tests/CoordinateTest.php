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

namespace LongitudeOne\GeoParser\Tests;

use LongitudeOne\Core\Enum\AxisEnum;
use LongitudeOne\GeoParser\Coordinate;
use PHPUnit\Framework\TestCase;

/**
 * Coordinate value object tests.
 */
class CoordinateTest extends TestCase
{
    /**
     * A coordinate without a cardinal direction has no axis.
     */
    public function testAllowsMissingAxis(): void
    {
        $coordinate = new Coordinate(-2);

        self::assertSame(-2, $coordinate->getValue());
        self::assertNull($coordinate->getAxis());
    }

    /**
     * A coordinate must retain its value and explicit axis.
     */
    public function testRetainsValueAndAxis(): void
    {
        $coordinate = new Coordinate(48.8566, AxisEnum::LATITUDE);

        self::assertSame(48.8566, $coordinate->getValue());
        self::assertSame(AxisEnum::LATITUDE, $coordinate->getAxis());
    }
}
