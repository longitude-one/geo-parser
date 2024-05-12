<?php

/**
 * This file is part of the LongitudeOne GeoParser project.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright LongitudeOne - Alexandre Tranchant - Derek J. Lambert.
 * Copyright 2024.
 *
 */

namespace LongitudeOne\Geo\String\Tests;

use LongitudeOne\Geo\String\Exception\ExceptionInterface;
use LongitudeOne\Geo\String\Exception\RangeException;
use LongitudeOne\Geo\String\Exception\UnexpectedValueException;
use LongitudeOne\Geo\String\Parser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Parser tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class ParserTest extends TestCase
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
        yield ['200N', RangeException::class, '[Range Error] Error: Degrees out of range -90 to 90 in value "200N"'];
        yield ['55:200:32', RangeException::class, '[Range Error] Error: Minutes greater than 60 in value "55:200:32"'];
        yield ['55:20:99', RangeException::class, '[Range Error] Error: Seconds greater than 60 in value "55:20:99"'];
        yield ['55°70.99\'', RangeException::class, '[Range Error] Error: Minutes greater than 60 in value "55°70.99\'"'];
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
     * @return \Generator<int, array{int|string, int|string|float|int[]|float[]}, null, void>
     */
    public static function dataSourceGood(): \Generator
    {
        yield [40, 40];
        yield ['40', 40];
        yield ['-40', -40];
        yield ['1E5', 100000];
        yield ['1e5', 100000];
        yield ['1e5°', 100000];
        yield ['40°', 40];
        yield ['-40°', -40];
        yield ['40° N', 40];
        yield ['40° S', -40];
        yield ['40°N', 40];
        yield ['40°S', -40];
        yield ['45.24', 45.24];
        yield ['45.24°', 45.24];
        yield ['+45.24°', 45.24];
        yield ['45.24° S', -45.24];
        yield ['45.83°N', 45.83];
        yield ['45.24°S', -45.24];
        yield ['40° 26\' 46" N', 40.44611111111111];
        yield ['40° 26\' 46"N', 40.44611111111111];
        yield ['40° 26\' 46" S', -40.44611111111111];
        yield ['40° 26\' 46"S', -40.44611111111111];
        yield ['40° 26′ 46″ N', 40.44611111111111];
        yield ['40° 26′ 46″N', 40.44611111111111];
        yield ['40° 26′ 46″ S', -40.44611111111111];
        yield ['40° 26′ 46″S', -40.44611111111111];
        yield ["40° 26\xe2\x80\xb2 46\xe2\x80\xb3 N", 40.44611111111111];
        yield ["40° 26\xe2\x80\xb2 46\xe2\x80\xb3N", 40.44611111111111];
        yield ["40° 26\xe2\x80\xb2 46\xe2\x80\xb3 S", -40.44611111111111];
        yield ["40° 26\xe2\x80\xb2 46\xe2\x80\xb3S", -40.44611111111111];
        yield ['40:26', 40.4333333333333333];
        yield ['40:26:46', 40.44611111111111];
        yield ['79:56:55W', -79.94861111111111];
        yield ['79:56:55 W', -79.94861111111111];
        yield ['40:26:46N', 40.44611111111111];
        yield ['40° N 79° W', [40, -79]];
        yield ['40 79', [40, 79]];
        yield ['40° 79°', [40, 79]];
        yield ['40, 79', [40, 79]];
        yield ['40°, 79°', [40, 79]];
        yield ['40° 26\' 46" N 79° 58\' 56" W', [40.44611111111111, -79.98222222222222]];
        yield ['40° 26\' N, 79° 58\' W', [40.4333333333333333, -79.96666666666666669]];
        yield ['40.4738° N, 79.553° W', [40.4738, -79.553]];
        yield ['40.4738° S, 79.553° W', [-40.4738, -79.553]];
        yield ['40° 26.222\' N 79° 58.52\' E', [40.43703333333333, 79.97533333333332]];
        yield ['40°26.222\'N 79°58.52\'E', [40.43703333333333, 79.97533333333332]];
        yield ['40°26.222\' 79°58.52\'', [40.43703333333333, 79.97533333333332]];
        yield ['40.222° -79.5852°', [40.222, -79.5852]];
        yield ['40.222°, -79.5852°', [40.222, -79.5852]];
        yield ['44°58\'53.9"N 93°19\'25.7"W', [44.98163888888888888, -93.32380555555557]];
        yield ['44°58\'53.9"N, 93°19\'25.7"W', [44.98163888888888888, -93.32380555555557]];
        yield ['79:56:55W 40:26:46N', [-79.94861111111111, 40.44611111111111]];
        yield ['79:56:55 W, 40:26:46 N', [-79.94861111111111, 40.44611111111111]];
        yield ['79°56′55″W, 40°26′46″N', [-79.94861111111111, 40.44611111111111]];
        yield ['1e-5°N 1e-5°W', [0.00001, -0.00001]];

        // Documentation tests

        // Simple single-signed values
        yield ['40', 40];
        yield [40, 40];
        yield [-40, -40];
        yield ['-40', -40];
        yield ['-8.543', -8.543];
        yield ['+132', 132];
        yield ['+77.2', 77.2];

        // Simple single-signed values with degree symbol
        yield ['40°', 40];
        yield ['-40°', -40];
        yield ['-5.234°', -5.234];
        yield ['+43°', 43];
        yield ['+38.43°', 38.43];

        // Single unsigned values with or without degree symbol, and cardinal direction
        yield ['40°N', 40];
        yield ['40 S', -40];
        yield ['56.242 E', 56.242];
        yield ['56.242 W', -56.242];

        // Single values of signed integer degrees with degree symbol, and decimal minutes with apostrophe
        yield ["40° 26.222'", 40.43703333333333];
        yield ["-65° 32.22'", -65.537];
        yield ["+165° 52.22'", 165.87033333333332];

        // Single values of unsigned integer degrees with degree symbol, decimal minutes with apostrophe, and cardinal direction
        yield ["40° 26.222' E", 40.43703333333333];
        yield ["65° 32.22' W", -65.537];

        // Single values of signed integer degrees with degree symbol, integer minutes with apostrophe, and optional integer or decimal seconds with quote
        yield ['40° 26\' 46"', 40.44611111111111];
        yield ['-79° 58\' 56"', -79.98222222222222];
        yield ['+93° 19\' 25.8"', 93.32383333333333];
        yield ['+120° 19\' 25.8"', 120.32383333333333];
    }

    /**
     * @dataProvider dataSourceBad
     *
     * @param class-string<ExceptionInterface> $exception
     */
    public function testBadValues(string $input, string $exception, string $message): void
    {
        self::expectException($exception);
        self::expectExceptionMessage($message);

        $parser = new Parser($input);

        $parser->parse();
    }

    /**
     * @param int|float|array<int|float> $expected
     *
     * @dataProvider dataSourceGood
     */
    public function testGoodValues(string|int $input, int|float|array $expected): void
    {
        $parser = new Parser($input);

        $value = $parser->parse();

        $this->assertEquals($expected, $value);
    }

    public function testParserReuse(): void
    {
        $parser = new Parser();

        foreach (static::dataSourceGood() as $data) {
            $input = $data[0];
            $expected = $data[1];

            $value = $parser->parse($input);

            self::assertEquals($expected, $value);
        }
    }

    /**
     * Test parser with multiple values from the documentation.
     *
     * @param array{0: (float|string|int), 1: (float|string|int)} $coordinates
     * @param array{0: float|int, 1: float|int}                   $expected
     */
    #[DataProvider('dataSourceFromDocumentation')]
    public function testParserWithMultipleValues(array $coordinates, array $expected): void
    {
        $separators = [' ', ',', ' ,', ', ', ' , '];
        foreach ($separators as $separator) {
            $input = implode($separator, $coordinates);
            $parser = new Parser($input);
            $value = $parser->parse();
            self::assertEquals($expected, $value);
        }
    }
}
