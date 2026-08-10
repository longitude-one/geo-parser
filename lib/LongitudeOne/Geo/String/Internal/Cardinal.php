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

namespace LongitudeOne\Geo\String\Internal;

use LongitudeOne\Geo\String\Axis;
use LongitudeOne\Geo\String\Exception\LogicException;

/**
 * Cardinal direction with its axis and numeric sign.
 *
 * @internal this enum supports the public Parser implementation
 */
enum Cardinal: string
{
    case EAST = 'e';
    case NORTH = 'n';
    case SOUTH = 's';
    case WEST = 'w';

    /**
     * Create a cardinal direction from a lexer token value.
     */
    public static function fromToken(string $token): self
    {
        return match (strtolower($token)) {
            self::EAST->value => self::EAST,
            self::NORTH->value => self::NORTH,
            self::SOUTH->value => self::SOUTH,
            self::WEST->value => self::WEST,
            default => throw new LogicException(sprintf('Token "%s" is not a cardinal direction.', $token)),
        };
    }

    /**
     * Return the axis constrained by this cardinal direction.
     */
    public function axis(): Axis
    {
        return match ($this) {
            self::NORTH, self::SOUTH => Axis::LATITUDE,
            self::EAST, self::WEST => Axis::LONGITUDE,
        };
    }

    /**
     * Return the numeric sign represented by the cardinal direction.
     */
    public function sign(): int
    {
        return match ($this) {
            self::NORTH, self::EAST => 1,
            self::SOUTH, self::WEST => -1,
        };
    }
}
