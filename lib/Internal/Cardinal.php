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

namespace LongitudeOne\GeoParser\Internal;

use LongitudeOne\Core\Diagnostic\DiagnosticValueFormatter;
use LongitudeOne\Core\Enum\AxisEnum;
use LongitudeOne\GeoParser\Exception\LogicException;

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
     *
     * @param string $token token to inspect
     */
    public static function fromToken(string $token): self
    {
        return match (strtolower($token)) {
            self::EAST->value => self::EAST,
            self::NORTH->value => self::NORTH,
            self::SOUTH->value => self::SOUTH,
            self::WEST->value => self::WEST,
            default => throw new LogicException(\sprintf('Token "%s" is not a cardinal direction.', DiagnosticValueFormatter::format($token))),
        };
    }

    /**
     * Return the axis constrained by this cardinal direction.
     */
    public function axis(): AxisEnum
    {
        return match ($this) {
            self::NORTH, self::SOUTH => AxisEnum::LATITUDE,
            self::EAST, self::WEST => AxisEnum::LONGITUDE,
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
