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

use LongitudeOne\Geo\String\Exception\LogicException;
use LongitudeOne\Geo\String\Exception\RangeException;

/**
 * Geographic coordinate axis and its cardinal constraints.
 */
enum Axis
{
    case LATITUDE;
    case LONGITUDE;

    /**
     * Return the axis represented by a cardinal token type.
     */
    public static function fromCardinalTokenType(?int $tokenType): self
    {
        return match ($tokenType) {
            Lexer::T_CARDINAL_LAT => self::LATITUDE,
            Lexer::T_CARDINAL_LON => self::LONGITUDE,
            default => throw new LogicException(sprintf('Token type %d is not a cardinal direction.', $tokenType)),
        };
    }

    /**
     * Whether a token type represents either coordinate axis.
     */
    public static function hasCardinalTokenType(int $tokenType): bool
    {
        return Lexer::T_CARDINAL_LAT === $tokenType || Lexer::T_CARDINAL_LON === $tokenType;
    }

    /**
     * Return the token type that matches cardinals for this axis.
     */
    public function cardinalTokenType(): int
    {
        return match ($this) {
            self::LATITUDE => Lexer::T_CARDINAL_LAT,
            self::LONGITUDE => Lexer::T_CARDINAL_LON,
        };
    }

    /**
     * Return the other axis in a coordinate pair.
     */
    public function other(): self
    {
        return match ($this) {
            self::LATITUDE => self::LONGITUDE,
            self::LONGITUDE => self::LATITUDE,
        };
    }

    /**
     * Return the parser exception code used when the axis is out of range.
     */
    public function rangeExceptionCode(): int
    {
        return match ($this) {
            self::LATITUDE => RangeException::LATITUDE_OUT_OF_RANGE,
            self::LONGITUDE => RangeException::LONGITUDE_OUT_OF_RANGE,
        };
    }

    /**
     * Return the maximum absolute degree value for the axis.
     */
    public function rangeLimit(): int
    {
        return match ($this) {
            self::LATITUDE => 90,
            self::LONGITUDE => 180,
        };
    }
}
