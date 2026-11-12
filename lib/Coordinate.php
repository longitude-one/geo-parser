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

use LongitudeOne\Core\Enum\AxisEnum;

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
