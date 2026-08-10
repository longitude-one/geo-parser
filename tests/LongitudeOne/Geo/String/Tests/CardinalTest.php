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

use LongitudeOne\Geo\String\AxisEnum;
use LongitudeOne\Geo\String\Exception\LogicException;
use LongitudeOne\Geo\String\Exception\RangeException;
use LongitudeOne\Geo\String\Internal\Cardinal;
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
     * @return \Generator<string, array{string, AxisEnum, int}, null, void>
     */
    public static function dataSourceCardinals(): \Generator
    {
        yield 'north' => ['N', AxisEnum::LATITUDE, 1];
        yield 'south' => ['s', AxisEnum::LATITUDE, -1];
        yield 'east' => ['E', AxisEnum::LONGITUDE, 1];
        yield 'west' => ['w', AxisEnum::LONGITUDE, -1];
    }

    /**
     * Axis constraints must retain their parser token, range, and pair order.
     */
    public function testAxisProvidesCoordinateConstraints(): void
    {
        self::assertSame(Lexer::T_CARDINAL_LAT, AxisEnum::LATITUDE->cardinalTokenType());
        self::assertSame(90, AxisEnum::LATITUDE->rangeLimit());
        self::assertSame(RangeException::LATITUDE_OUT_OF_RANGE, AxisEnum::LATITUDE->rangeExceptionCode());
        self::assertSame(AxisEnum::LONGITUDE, AxisEnum::LATITUDE->other());

        self::assertSame(Lexer::T_CARDINAL_LON, AxisEnum::LONGITUDE->cardinalTokenType());
        self::assertSame(180, AxisEnum::LONGITUDE->rangeLimit());
        self::assertSame(RangeException::LONGITUDE_OUT_OF_RANGE, AxisEnum::LONGITUDE->rangeExceptionCode());
        self::assertSame(AxisEnum::LATITUDE, AxisEnum::LONGITUDE->other());
    }

    /**
     * Cardinal directions must expose their axis and sign independently of parsing.
     *
     * @param string   $token cardinal token
     * @param AxisEnum $axis  expected geographic axis
     * @param int      $sign  expected numeric sign
     */
    #[DataProvider('dataSourceCardinals')]
    public function testCardinalMapsToAxisAndSign(string $token, AxisEnum $axis, int $sign): void
    {
        $cardinal = Cardinal::fromToken($token);

        self::assertSame($axis, $cardinal->axis());
        self::assertSame($sign, $cardinal->sign());
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
