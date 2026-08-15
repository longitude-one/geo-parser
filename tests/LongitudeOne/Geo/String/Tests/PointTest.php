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

use LongitudeOne\Geo\String\AxisEnum;
use LongitudeOne\Geo\String\Coordinate;
use LongitudeOne\Geo\String\Point;
use PHPUnit\Framework\TestCase;

/**
 * Point value object tests.
 */
class PointTest extends TestCase
{
    /**
     * A point must retain its two coordinate objects in order.
     */
    public function testRetainsCoordinates(): void
    {
        $first = new Coordinate(48.8566, AxisEnum::LATITUDE);
        $second = new Coordinate(2.3522, AxisEnum::LONGITUDE);

        $point = new Point($first, $second);

        self::assertSame($first, $point->getFirst());
        self::assertSame($second, $point->getSecond());
    }
}
