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

namespace LongitudeOne\GeoParser;

/**
 * A pair of coordinate values.
 */
final readonly class Point
{
    /**
     * Create a point from its first and second coordinates.
     */
    public function __construct(
        private readonly Coordinate $first,
        private readonly Coordinate $second,
    ) {
    }

    /**
     * Get the first coordinate.
     */
    public function getFirst(): Coordinate
    {
        return $this->first;
    }

    /**
     * Get the second coordinate.
     */
    public function getSecond(): Coordinate
    {
        return $this->second;
    }
}
