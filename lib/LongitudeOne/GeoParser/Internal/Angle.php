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

namespace LongitudeOne\GeoParser\Internal;

/**
 * Coordinate angle expressed in degrees, optional minutes, and optional seconds.
 *
 * @internal this value object supports the parser implementation and is not part of the public API
 */
final readonly class Angle
{
    /**
     * Create an angle from its degree, minute, and second components.
     *
     * @param int|float  $degrees whole or decimal degree component
     * @param float|null $minutes optional minute component
     * @param float|null $seconds optional second component
     */
    public function __construct(
        private int|float $degrees,
        private ?float $minutes = null,
        private ?float $seconds = null,
    ) {
    }

    /**
     * Convert the angle to decimal degrees while preserving current precision.
     */
    public function toDecimalDegrees(): int|float
    {
        if (null === $this->minutes) {
            return $this->degrees;
        }

        $minutes = $this->minutes / 60;

        if (null !== $this->seconds) {
            $minutes += $this->normalizeFraction($this->seconds / 3600);
        }

        return $this->degrees + $this->normalizeFraction($minutes);
    }

    /**
     * Normalize a fractional component while preserving the parser's historical precision.
     */
    private function normalizeFraction(float $value): float
    {
        return (float) (string) $value;
    }
}
