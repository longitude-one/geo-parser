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
use LongitudeOne\Geo\String\Coordinate;
use LongitudeOne\Geo\String\Exception\ExceptionInterface;
use LongitudeOne\Geo\String\Exception\RangeException;
use LongitudeOne\Geo\String\Exception\UnexpectedValueException;
use LongitudeOne\Geo\String\Point;

/**
 * Shared parser examples for the focused parser test suites.
 *
 * Keeping the accepted values, invalid inputs, and documentation examples in
 * one place prevents the legacy numeric API and the structured-coordinate API
 * from silently testing different grammars. This class deliberately contains
 * no test methods; PHPUnit test cases consume its data providers.
 */
final class ParserDataProvider
{
    /**
     * @return \Generator<int, array{string, class-string<ExceptionInterface>, string}, null, void>
     */
    public static function dataSourceBad(): \Generator
    {
        yield ['-40°N 45°W', UnexpectedValueException::class, '[Syntax Error] line 0, col 5: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "N" in value "-40°N 45°W"'];
        yield ['+40°N 45°W', UnexpectedValueException::class, '[Syntax Error] line 0, col 5: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "N" in value "+40°N 45°W"'];
        yield ['40°N +45°W', UnexpectedValueException::class, '[Syntax Error] line 0, col 6: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "+" in value "40°N +45°W"'];
        yield ['40°N -45W', UnexpectedValueException::class, '[Syntax Error] line 0, col 6: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "-" in value "40°N -45W"'];
        yield ['40N -45°W', UnexpectedValueException::class, '[Syntax Error] line 0, col 4: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "-" in value "40N -45°W"'];
        yield ['40N 45°W', UnexpectedValueException::class, '[Syntax Error] line 0, col 6: Error: Expected LongitudeOne\Geo\String\Lexer::T_CARDINAL_LON, got "°" in value "40N 45°W"'];
        yield ['40°N 45°S', UnexpectedValueException::class, '[Syntax Error] line 0, col 10: Error: Expected LongitudeOne\Geo\String\Lexer::T_CARDINAL_LON, got "S" in value "40°N 45°S"'];
        yield ['40°W 45°E', UnexpectedValueException::class, '[Syntax Error] line 0, col 10: Error: Expected LongitudeOne\Geo\String\Lexer::T_CARDINAL_LAT, got "E" in value "40°W 45°E"'];
        yield ['40° 45', UnexpectedValueException::class, '[Syntax Error] line 0, col -1: Error: Expected LongitudeOne\Geo\String\Lexer::T_APOSTROPHE, got end of string. in value "40° 45"'];
        yield ['40°, 45', UnexpectedValueException::class, '[Syntax Error] line 0, col -1: Error: Expected LongitudeOne\Geo\String\Lexer::T_DEGREE, got end of string. in value "40°, 45"'];
        yield ['40N 45', UnexpectedValueException::class, '[Syntax Error] line 0, col -1: Error: Expected LongitudeOne\Geo\String\Lexer::T_CARDINAL_LON, got end of string. in value "40N 45"'];
        yield ['40 45W', UnexpectedValueException::class, '[Syntax Error] line 0, col 5: Error: Expected end of string, got "W" in value "40 45W"'];
        yield ['-40.757° 45°W', UnexpectedValueException::class, '[Syntax Error] line 0, col 14: Error: Expected end of string, got "W" in value "-40.757° 45°W"'];
        yield ['40.757°N -45.567°W', UnexpectedValueException::class, '[Syntax Error] line 0, col 10: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "-" in value "40.757°N -45.567°W"'];
        yield ['44°58\'53.9N 93°19\'25.8"W', UnexpectedValueException::class, '[Syntax Error] line 0, col 11: Error: Expected LongitudeOne\Geo\String\Lexer::T_QUOTE, got "N" in value "44°58\'53.9N 93°19\'25.8"W"'];
        yield ['40:26\'', UnexpectedValueException::class, '[Syntax Error] line 0, col 5: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "\'" in value "40:26\'"'];
        yield ['132.4432:', UnexpectedValueException::class, '[Syntax Error] line 0, col 8: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got ":" in value "132.4432:"'];
        yield ['55:34:22°', UnexpectedValueException::class, '[Syntax Error] line 0, col 8: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "°" in value "55:34:22°"'];
        yield ['55:34.22', UnexpectedValueException::class, '[Syntax Error] line 0, col 3: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER, got "34.22" in value "55:34.22"'];
        yield ['55#34.22', UnexpectedValueException::class, '[Syntax Error] line 0, col 2: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "#" in value "55#34.22"'];
        yield ['100N', RangeException::class, '[RangeException] Latitude must be between -90 and 90, got "100N".'];
        yield ['190W', RangeException::class, '[RangeException] Longitude must be between -180 and 180, got "190W".'];
        yield ['55:200:32', RangeException::class, '[RangeException] Minutes must be between 0 and 59, got "55:200:32".'];
        yield ['55:20:99', RangeException::class, '[RangeException] Seconds must be between 0 and 59, got "55:20:99".'];
        yield ['55°70.99\'', RangeException::class, '[RangeException] Minutes must be between 0 and 59, got "55°70.99\'".'];
        // Issue #21
        yield ['55°60.17\'', RangeException::class, '[RangeException] Minutes must be between 0 and 59, got "55°60.17\'".'];
        yield ['55:60:32', RangeException::class, '[RangeException] Minutes must be between 0 and 59, got "55:60:32".'];
        yield ['55:20:60', RangeException::class, '[RangeException] Seconds must be between 0 and 59, got "55:20:60".'];
        yield ['180°20\'20"W', RangeException::class, '[RangeException] Longitude must be between -180 and 180, got "180°20\'20"W".'];
        yield ['180°20\'20"E', RangeException::class, '[RangeException] Longitude must be between -180 and 180, got "180°20\'20"E".'];
        yield ['90°20\'20"S', RangeException::class, '[RangeException] Latitude must be between -90 and 90, got "90°20\'20"S".'];
        yield ['90°20\'20"N', RangeException::class, '[RangeException] Latitude must be between -90 and 90, got "90°20\'20"N".'];
    }

    /**
     * @return \Generator<int, array{0: (int|float|string)[], 1:(int|float|string)[]}, null ,void>
     */
    public static function dataSourceFromDocumentation(): \Generator
    {
        // Simple single-signed values
        yield [[40, 45], [40, 45]];
        yield [[-40, -45], [-40, -45]];
        yield [[-8.543, -45.543], [-8.543, -45.543]];
        yield [['+132', '+45'], ['+132', '+45']];
        yield [['+77.2', '+45'], ['+77.2', '+45']];

        // Simple single-signed values with degree symbol
        yield [['40°', '45°'], [40, 45]];
        yield [['-40°', '-45°'], [-40, -45]];
        yield [['-5.234°', '-45.543°'], [-5.234, -45.543]];
        yield [['+43°', '+45°'], [43, 45]];
        yield [['+38.43°', '+45.543°'], [38.43, 45.543]];

        // Single unsigned values with or without degree symbol, and cardinal direction
        yield [['40°N', '45°W'], [40, -45]];
        yield [['40 S', '45 E'], [-40, 45]];
        yield [['56.242 S', '45.543 W'], [-56.242, -45.543]];

        // Single values of signed integer degrees with degree symbol, and decimal minutes with apostrophe
        yield [['40° 26.222\'', '-45° 32.22\''], [40.43703333333333, -45.537]];
        yield [['-65° 32.22\'', '+45° 32.22\''], [-65.537, 45.537]];
        yield [['+165° 52.22\'', '-45° 32.22\''], [165.87033333333332, -45.537]];

        // Single values of unsigned integer degrees with degree symbol, decimal minutes with apostrophe, and cardinal direction
        yield [['40° 26.222\' N', '45° 32.22\' W'], [40.43703333333333, -45.537]];
        yield [['65° 32.22\' S', '45° 32.22\' E'], [-65.537, 45.537]];

        // Single values of signed integer degrees with degree symbol, integer minutes with apostrophe, and optional integer or decimal seconds with quote
        yield [['40° 26\' 46"', '-45° 32\' 22"'], [40.44611111111111, -45.53944444444444]];
        yield [['-79° 58\' 56"', '+45° 32\' 22"'], [-79.98222222222222, 45.53944444444444]];
        yield [['+93° 19\' 25.8"', '-45° 32\' 22"'], [93.32383333333333, -45.53944444444444]];

        // Single values of unsigned integer degrees with degree symbol, integer minutes with apostrophe, optional integer or decimal seconds with quote, and cardinal direction
        yield [['40° 26\' 46" S', '45° 32\' 22" W'], [-40.44611111111111, -45.53944444444444]];
        yield [['89° 58\' 56" N', '99° 32\' 22" E'], [89.98222222222222, 99.53944444444444]];
        yield [['44° 58\' 53.9" N', '44° 58\' 53.9" E'], [44.98163888888888888, 44.98163888888888888]];

        // Single values of unsigned integer degrees with colon symbol, integer minutes with, optional colon and integer or decimal seconds, and cardinal direction
        yield [['40:26:46 N', '45:32:22 W'], [40.44611111111111, -45.53944444444444]];
        yield [['44:58:53.9 N', '99:58:56 W'], [44.98163888888889, -99.98222222222222]];
    }

    /**
     * @return \Generator<int, array{int|string, int|float|array<int, int|float>, Coordinate|Point}, null, void>
     */
    public static function dataSourceGood(): \Generator
    {
        yield [40, 40, new Coordinate(40)];
        yield ['40', 40, new Coordinate(40)];
        yield ['-40', -40, new Coordinate(-40)];
        yield ['1E5', 100000, new Coordinate(100000)];
        yield ['1e5', 100000, new Coordinate(100000)];
        yield ['1e5°', 100000, new Coordinate(100000)];
        yield ['40°', 40, new Coordinate(40)];
        yield ['-40°', -40, new Coordinate(-40)];
        yield ['40° N', 40, new Coordinate(40, AxisEnum::LATITUDE)];
        yield ['40° S', -40, new Coordinate(-40, AxisEnum::LATITUDE)];
        yield ['40°N', 40, new Coordinate(40, AxisEnum::LATITUDE)];
        yield ['40°S', -40, new Coordinate(-40, AxisEnum::LATITUDE)];
        yield ['45.24', 45.24, new Coordinate(45.24)];
        yield ['45.24°', 45.24, new Coordinate(45.24)];
        yield ['+45.24°', 45.24, new Coordinate(45.24)];
        yield ['45.24° S', -45.24, new Coordinate(-45.24, AxisEnum::LATITUDE)];
        yield ['45.83°N', 45.83, new Coordinate(45.83, AxisEnum::LATITUDE)];
        yield ['45.24°S', -45.24, new Coordinate(-45.24, AxisEnum::LATITUDE)];
        yield ['40° 26\' 46" N', 40.44611111111111, new Coordinate(40.44611111111111, AxisEnum::LATITUDE)];
        yield ['40° 26\' 46"N', 40.44611111111111, new Coordinate(40.44611111111111, AxisEnum::LATITUDE)];
        yield ['40° 26\' 46" S', -40.44611111111111, new Coordinate(-40.44611111111111, AxisEnum::LATITUDE)];
        yield ['40° 26\' 46"S', -40.44611111111111, new Coordinate(-40.44611111111111, AxisEnum::LATITUDE)];
        yield ['40° 26′ 46″ N', 40.44611111111111, new Coordinate(40.44611111111111, AxisEnum::LATITUDE)];
        yield ['40° 26′ 46″N', 40.44611111111111, new Coordinate(40.44611111111111, AxisEnum::LATITUDE)];
        yield ['40° 26′ 46″ S', -40.44611111111111, new Coordinate(-40.44611111111111, AxisEnum::LATITUDE)];
        yield ['40° 26′ 46″S', -40.44611111111111, new Coordinate(-40.44611111111111, AxisEnum::LATITUDE)];
        yield ["40° 26\xe2\x80\xb2 46\xe2\x80\xb3 N", 40.44611111111111, new Coordinate(40.44611111111111, AxisEnum::LATITUDE)];
        yield ["40° 26\xe2\x80\xb2 46\xe2\x80\xb3N", 40.44611111111111, new Coordinate(40.44611111111111, AxisEnum::LATITUDE)];
        yield ["40° 26\xe2\x80\xb2 46\xe2\x80\xb3 S", -40.44611111111111, new Coordinate(-40.44611111111111, AxisEnum::LATITUDE)];
        yield ["40° 26\xe2\x80\xb2 46\xe2\x80\xb3S", -40.44611111111111, new Coordinate(-40.44611111111111, AxisEnum::LATITUDE)];
        yield ['79:56:55W', -79.94861111111111, new Coordinate(-79.94861111111111, AxisEnum::LONGITUDE)];
        yield ['79:56:55 W', -79.94861111111111, new Coordinate(-79.94861111111111, AxisEnum::LONGITUDE)];
        yield ['40:26:46N', 40.44611111111111, new Coordinate(40.44611111111111, AxisEnum::LATITUDE)];
        yield ['40° N 79° W', [40, -79], new Point(new Coordinate(40, AxisEnum::LATITUDE), new Coordinate(-79, AxisEnum::LONGITUDE))];
        yield ['40 79', [40, 79], new Point(new Coordinate(40, null), new Coordinate(79, null))];
        yield ['40° 79°', [40, 79], new Point(new Coordinate(40, null), new Coordinate(79, null))];
        yield ['40, 79', [40, 79], new Point(new Coordinate(40, null), new Coordinate(79, null))];
        yield ['40°, 79°', [40, 79], new Point(new Coordinate(40, null), new Coordinate(79, null))];
        yield ['40° 26\' 46" N 79° 58\' 56" W', [40.44611111111111, -79.98222222222222], new Point(new Coordinate(40.44611111111111, AxisEnum::LATITUDE), new Coordinate(-79.98222222222222, AxisEnum::LONGITUDE))];
        yield ['40° 26\' N, 79° 58\' W', [40.4333333333333333, -79.96666666666666669], new Point(new Coordinate(40.4333333333333333, AxisEnum::LATITUDE), new Coordinate(-79.96666666666666669, AxisEnum::LONGITUDE))];
        yield ['40.4738° N, 79.553° W', [40.4738, -79.553], new Point(new Coordinate(40.4738, AxisEnum::LATITUDE), new Coordinate(-79.553, AxisEnum::LONGITUDE))];
        yield ['40.4738° S, 79.553° W', [-40.4738, -79.553], new Point(new Coordinate(-40.4738, AxisEnum::LATITUDE), new Coordinate(-79.553, AxisEnum::LONGITUDE))];
        yield ['40° 26.222\' N 79° 58.52\' E', [40.43703333333333, 79.97533333333332], new Point(new Coordinate(40.43703333333333, AxisEnum::LATITUDE), new Coordinate(79.97533333333332, AxisEnum::LONGITUDE))];
        yield ['40°26.222\' 79°58.52\'', [40.43703333333333, 79.97533333333332], new Point(new Coordinate(40.43703333333333, null), new Coordinate(79.97533333333332, null))];
        yield ['40.222° -79.5852°', [40.222, -79.5852], new Point(new Coordinate(40.222), new Coordinate(-79.5852))];
        yield ['40.222°, -79.5852°', [40.222, -79.5852], new Point(new Coordinate(40.222), new Coordinate(-79.5852))];
        yield ['44°58\'53.9"N 93°19\'25.7"W', [44.98163888888888888, -93.32380555555557], new Point(new Coordinate(44.98163888888888888, AxisEnum::LATITUDE), new Coordinate(-93.32380555555557, AxisEnum::LONGITUDE))];
        yield ['44°58\'53.9"N, 93°19\'25.7"W', [44.98163888888888888, -93.32380555555557], new Point(new Coordinate(44.9816388888888888, AxisEnum::LATITUDE), new Coordinate(-93.32380555555557, AxisEnum::LONGITUDE))];
        yield ['79:56:55W 40:26:46N', [-79.94861111111111, 40.44611111111111], new Point(new Coordinate(-79.94861111111111, AxisEnum::LONGITUDE), new Coordinate(40.44611111111111, AxisEnum::LATITUDE))];
        yield ['79:56:55 W, 40:26:46 N', [-79.94861111111111, 40.44611111111111], new Point(new Coordinate(-79.94861111111111, AxisEnum::LONGITUDE), new Coordinate(40.44611111111111, AxisEnum::LATITUDE))];
        yield ['79°56′55″W, 40°26′46″N', [-79.94861111111111, 40.44611111111111], new Point(new Coordinate(-79.94861111111111, AxisEnum::LONGITUDE), new Coordinate(40.44611111111111, AxisEnum::LATITUDE))];
        yield ['1e-5°N 1e-5°W', [0.00001, -0.00001], new Point(new Coordinate(0.00001, AxisEnum::LATITUDE), new Coordinate(-0.00001, AxisEnum::LONGITUDE))];

        // Issue #21
        yield ['180°00\'00"W, 90°00\'00"S', [-180, -90], new Point(new Coordinate(-180, AxisEnum::LONGITUDE), new Coordinate(-90, AxisEnum::LATITUDE))];
        yield ['180°00\'00"E, 90°00\'00"N', [180, 90], new Point(new Coordinate(180, AxisEnum::LONGITUDE), new Coordinate(90, AxisEnum::LATITUDE))];
        yield ['180:00:00E, 90:00:00N', [180, 90], new Point(new Coordinate(180, AxisEnum::LONGITUDE), new Coordinate(90, AxisEnum::LATITUDE))];
        yield ['180:00:00W, 90:00:00S', [-180, -90], new Point(new Coordinate(-180, AxisEnum::LONGITUDE), new Coordinate(-90, AxisEnum::LATITUDE))];
        yield ['55°17.60\'', 55.29333333333333, new Coordinate(55.29333333333333)];
        yield [180, 180, new Coordinate(180)];
        yield ['180.0', 180.0, new Coordinate(180.0)];
        yield [-180, -180, new Coordinate(-180)];
        yield ['-180.0', -180.0, new Coordinate(-180.0)];

        // Documentation tests

        // Simple single-signed values
        yield ['40', 40, new Coordinate(40)];
        yield [40, 40, new Coordinate(40)];
        yield [-40, -40, new Coordinate(-40)];
        yield ['-40', -40, new Coordinate(-40)];
        yield ['-8.543', -8.543, new Coordinate(-8.543)];
        yield ['+132', 132, new Coordinate(132)];
        yield ['+77.2', 77.2, new Coordinate(77.2)];

        // Simple single-signed values with degree symbol
        yield ['40°', 40, new Coordinate(40)];
        yield ['-40°', -40, new Coordinate(-40)];
        yield ['-5.234°', -5.234, new Coordinate(-5.234)];
        yield ['+43°', 43, new Coordinate(43)];
        yield ['+38.43°', 38.43, new Coordinate(38.43)];

        // Single unsigned values with or without degree symbol, and cardinal direction
        yield ['40°N', 40, new Coordinate(40, AxisEnum::LATITUDE)];
        yield ['40 S', -40, new Coordinate(-40, AxisEnum::LATITUDE)];
        yield ['56.242 E', 56.242, new Coordinate(56.242, AxisEnum::LONGITUDE)];
        yield ['56.242 W', -56.242, new Coordinate(-56.242, AxisEnum::LONGITUDE)];

        // Single values of signed integer degrees with degree symbol, and decimal minutes with apostrophe
        yield ["40° 26.222'", 40.43703333333333, new Coordinate(40.43703333333333)];
        yield ["-65° 32.22'", -65.537, new Coordinate(-65.537)];
        yield ["+165° 52.22'", 165.87033333333332, new Coordinate(165.87033333333332)];

        // Single values of unsigned integer degrees with degree symbol, decimal minutes with apostrophe, and cardinal direction
        yield ["40° 26.222' E", 40.43703333333333, new Coordinate(40.43703333333333, AxisEnum::LONGITUDE)];
        yield ["65° 32.22' W", -65.537, new Coordinate(-65.537, AxisEnum::LONGITUDE)];

        // Single values of signed integer degrees with degree symbol, integer minutes with apostrophe, and optional integer or decimal seconds with quote
        yield ['40° 26\' 46"', 40.44611111111111, new Coordinate(40.44611111111111)];
        yield ['-79° 58\' 56"', -79.98222222222222, new Coordinate(-79.98222222222222)];
        yield ['+93° 19\' 25.8"', 93.32383333333333, new Coordinate(93.32383333333333)];
        yield ['+120° 19\' 25.8"', 120.32383333333333, new Coordinate(120.32383333333333)];
    }
}
