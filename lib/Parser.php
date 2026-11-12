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

use LongitudeOne\GeoParser\Exception\InvalidArgumentException;
use LongitudeOne\GeoParser\Internal\CoordinateParser;

/**
 * Parse geographic coordinate input.
 */
final class Parser
{
    /**
     * Input retained for parsing when no value is passed to parse().
     */
    private string|int|float|null $input;

    /**
     * Create a parser with an optional coordinate input.
     */
    public function __construct(string|int|float|null $input = null)
    {
        $this->input = $input;
    }

    /**
     * Parse a native numeric value or textual coordinate input.
     *
     * @return float|int|array<int, int|float>
     */
    public function parse(string|int|float|null $input = null): float|int|array
    {
        if (null !== $input) {
            $this->input = $input;
        }

        if (null === $this->input) {
            throw new InvalidArgumentException('An input value must be provided to either the constructor or the parse method.');
        }

        if (\is_int($this->input) || \is_float($this->input)) {
            return $this->input;
        }

        return (new CoordinateParser($this->input))->parse();
    }

    public function parseAsCoordinates(string|int|float|null $input = null): Coordinate|Point
    {
        if (null !== $input) {
            $this->input = $input;
        }

        if (null === $this->input) {
            throw new InvalidArgumentException('An input value must be provided to either the constructor or the parseAsCoordinates method.');
        }

        if (\is_int($this->input) || \is_float($this->input)) {
            return new Coordinate($this->input);
        }

        return (new CoordinateParser($this->input))->parseAsCoordinates();
    }
}
