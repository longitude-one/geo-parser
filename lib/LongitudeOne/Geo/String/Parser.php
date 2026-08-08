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

namespace LongitudeOne\Geo\String;

use LongitudeOne\Geo\String\Exception\InvalidArgumentException;

/**
 * Parse geographic coordinate input.
 */
class Parser
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

        if (is_int($this->input) || is_float($this->input)) {
            return $this->input;
        }

        return (new CoordinateParser($this->input))->parse();
    }
}
