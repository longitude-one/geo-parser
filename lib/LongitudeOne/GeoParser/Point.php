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
