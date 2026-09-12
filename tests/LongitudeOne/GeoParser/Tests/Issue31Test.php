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

namespace LongitudeOne\GeoParser\Tests;

use LongitudeOne\Core\Enum\AxisEnum;
use LongitudeOne\GeoParser\Coordinate;
use LongitudeOne\GeoParser\Exception\RangeException;
use LongitudeOne\GeoParser\Exception\UnexpectedValueException;
use LongitudeOne\GeoParser\Parser;
use LongitudeOne\GeoParser\Point;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Issue #31: a numeric sign and hemisphere describe the same direction.
 */
final class Issue31Test extends TestCase
{
    /**
     * @return \Generator<string, array{string, Coordinate|Point}, null, void>
     */
    public static function acceptedInputs(): \Generator
    {
        foreach (['N' => AxisEnum::LATITUDE, 'S' => AxisEnum::LATITUDE, 'E' => AxisEnum::LONGITUDE, 'W' => AxisEnum::LONGITUDE] as $cardinal => $axis) {
            $sign = in_array($cardinal, ['S', 'W'], true) ? '-' : '+';
            $factor = '-' === $sign ? -1 : 1;
            foreach (['40' => 40, '40.5' => 40.5, "40°30'" => 40.5, '40°30′36″' => 40.51, '40:30:36' => 40.51, '1e-5' => 0.00001, '0' => 0] as $angle => $value) {
                yield $sign.$angle.$cardinal => [$sign.$angle.$cardinal, new Coordinate($factor * $value, $axis)];
                yield $angle.$cardinal => [$angle.$cardinal, new Coordinate($factor * $value, $axis)];
            }
        }

        yield 'east example' => ['+75E', new Coordinate(75, AxisEnum::LONGITUDE)];
        yield 'west example' => ['-75W', new Coordinate(-75, AxisEnum::LONGITUDE)];
        yield 'latitude boundary' => ['-90S', new Coordinate(-90, AxisEnum::LATITUDE)];
        yield 'longitude boundary' => ['-180W', new Coordinate(-180, AxisEnum::LONGITUDE)];
        yield 'signed only' => ['-75', new Coordinate(-75)];
        yield 'positive only' => ['+40', new Coordinate(40)];
        yield 'signed pair' => ['+40N, -75W', new Point(new Coordinate(40, AxisEnum::LATITUDE), new Coordinate(-75, AxisEnum::LONGITUDE))];
        yield 'reverse pair' => ['-75W +40N', new Point(new Coordinate(-75, AxisEnum::LONGITUDE), new Coordinate(40, AxisEnum::LATITUDE))];
        yield 'second signed' => ['40.757°N -45.567°W', new Point(new Coordinate(40.757, AxisEnum::LATITUDE), new Coordinate(-45.567, AxisEnum::LONGITUDE))];
        yield 'first signed' => ['+40°N 45°W', new Point(new Coordinate(40, AxisEnum::LATITUDE), new Coordinate(-45, AxisEnum::LONGITUDE))];
        yield 'no axes' => ['+40, -75', new Point(new Coordinate(40), new Coordinate(-75))];
    }

    /**
     * @return \Generator<int, array{string, class-string<\Exception>}, null, void>
     */
    public static function rejectedInputs(): \Generator
    {
        foreach (['-75E', '+75W', '-40N', '+40S', '-0N', '+0S', '-0E', '+0W', '-40:30N', '+40°30′36″S', '+40N +75W', '-40S -75E', '+40N -45S', '+75E -45W', '++40N', '--75W', '+40N -75', '+40N -75W trailing'] as $input) {
            yield [$input, UnexpectedValueException::class];
        }

        foreach (['+91N', '-91S', '+181E', '-181W', '-90°00′01″S', '-180°00′01″W', '-40:60S', '-40:30:60S'] as $input) {
            yield [$input, RangeException::class];
        }
    }

    #[DataProvider('acceptedInputs')]
    public function testNormalizesConsistentDirections(string $input, Coordinate|Point $expected): void
    {
        $parser = new Parser();
        $values = $expected instanceof Point ? [$expected->getFirst()->getValue(), $expected->getSecond()->getValue()] : $expected->getValue();

        self::assertEquals($values, $parser->parse($input));
        self::assertEquals($expected, $parser->parseAsCoordinates($input));
        self::assertEquals(new Coordinate(-75), $parser->parseAsCoordinates('-75'));
    }

    /**
     * @param class-string<\Exception> $exception
     */
    #[DataProvider('rejectedInputs')]
    public function testRejectsInvalidNumericInput(string $input, string $exception): void
    {
        self::expectException($exception);

        (new Parser($input))->parse();
    }

    /**
     * @param class-string<\Exception> $exception
     */
    #[DataProvider('rejectedInputs')]
    public function testRejectsInvalidStructuredInput(string $input, string $exception): void
    {
        self::expectException($exception);

        (new Parser($input))->parseAsCoordinates();
    }
}
