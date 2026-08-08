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

namespace LongitudeOne\Geo\String\Tests;

use LongitudeOne\Geo\String\Axis;
use LongitudeOne\Geo\String\Cardinal;
use LongitudeOne\Geo\String\Exception\LogicException;
use LongitudeOne\Geo\String\Exception\RangeException;
use LongitudeOne\Geo\String\Lexer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Cardinal direction and axis tests.
 */
class CardinalTest extends TestCase
{
    /**
     * Provide cardinal tokens with their axis and numeric sign.
     *
     * @return \Generator<string, array{string, Axis, int}, null, void>
     */
    public static function dataSourceCardinals(): \Generator
    {
        yield 'north' => ['N', Axis::LATITUDE, 1];
        yield 'south' => ['s', Axis::LATITUDE, -1];
        yield 'east' => ['E', Axis::LONGITUDE, 1];
        yield 'west' => ['w', Axis::LONGITUDE, -1];
    }

    /**
     * Axis constraints must retain their parser token, range, and pair order.
     */
    public function testAxisProvidesCoordinateConstraints(): void
    {
        self::assertSame(Lexer::T_CARDINAL_LAT, Axis::LATITUDE->cardinalTokenType());
        self::assertSame(90, Axis::LATITUDE->rangeLimit());
        self::assertSame(RangeException::LATITUDE_OUT_OF_RANGE, Axis::LATITUDE->rangeExceptionCode());
        self::assertSame(Axis::LONGITUDE, Axis::LATITUDE->other());

        self::assertSame(Lexer::T_CARDINAL_LON, Axis::LONGITUDE->cardinalTokenType());
        self::assertSame(180, Axis::LONGITUDE->rangeLimit());
        self::assertSame(RangeException::LONGITUDE_OUT_OF_RANGE, Axis::LONGITUDE->rangeExceptionCode());
        self::assertSame(Axis::LATITUDE, Axis::LONGITUDE->other());
    }

    /**
     * Cardinal directions must expose their axis and sign independently of parsing.
     *
     * @param string $token cardinal token
     * @param Axis   $axis  expected geographic axis
     * @param int    $sign  expected numeric sign
     */
    #[DataProvider('dataSourceCardinals')]
    public function testCardinalMapsToAxisAndSign(string $token, Axis $axis, int $sign): void
    {
        $cardinal = Cardinal::fromToken($token);

        self::assertSame($axis, $cardinal->axis());
        self::assertSame($sign, $cardinal->sign());
    }

    /**
     * Only latitude and longitude cardinal token types must be recognized.
     */
    public function testRecognizesCardinalTokenTypes(): void
    {
        self::assertTrue(Axis::hasCardinalTokenType(Lexer::T_CARDINAL_LAT));
        self::assertTrue(Axis::hasCardinalTokenType(Lexer::T_CARDINAL_LON));
        self::assertFalse(Axis::hasCardinalTokenType(Lexer::T_INTEGER));
    }

    /**
     * Unknown cardinal tokens must raise a catchable library exception.
     */
    public function testUnknownCardinalTokenThrowsLibraryLogicException(): void
    {
        self::expectException(LogicException::class);
        self::expectExceptionMessage('Token "X" is not a cardinal direction.');

        Cardinal::fromToken('X');
    }
}
