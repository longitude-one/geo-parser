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



namespace LongitudeOne\GeoParser\Tests;

use LongitudeOne\Core\Enum\AxisEnum;
use LongitudeOne\GeoParser\Coordinate;
use LongitudeOne\GeoParser\Point;
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
