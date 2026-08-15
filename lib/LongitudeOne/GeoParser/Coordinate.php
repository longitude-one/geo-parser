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
 * A coordinate value with its optional geographic axis.
 */
final readonly class Coordinate
{
    /**
     * Create a coordinate value.
     *
     * @param int|float     $value coordinate value
     * @param AxisEnum|null $axis  geographic axis, when specified by the input
     */
    public function __construct(
        private readonly int|float $value,
        private readonly ?AxisEnum $axis = null,
    ) {
    }

    /**
     * Get the geographic axis if defined.
     */
    public function getAxis(): ?AxisEnum
    {
        return $this->axis;
    }

    /**
     * Get the coordinate value.
     */
    public function getValue(): int|float
    {
        return $this->value;
    }
}
