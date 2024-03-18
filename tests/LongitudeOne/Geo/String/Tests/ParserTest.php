<?php

/**
 * This file is part of the LongitudeOne GeoParser project.
 *
 * PHP 7.4
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
     * @return \Generator<array{string, class-string<ExceptionInterface>, string}>
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
     * @return \Generator<array{int|string|float, int|string|float|int[]|float[]}>
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
        yield [45.24, 45.24];
        yield ['45.24°', 45.24];
        yield ['+45.24°', 45.24];
        yield ['45.24° S', -45.24];
        yield ['45.24°N', 45.24];
        yield ['45.24°S', -45.24];
        yield ['40° 26\' 46" N', 40.44611111111111];
        yield ['40° 26\' 46"N', 40.44611111111111];
        yield ['40° 26\' 46" S', -40.44611111111111];
        yield ['40° 26\' 46"S', -40.44611111111111];
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
        yield ['40° 26.222\' N 79° 58.52\' E', [40.43703333333333, 79.97533333333334]];
        yield ['40°26.222\'N 79°58.52\'E', [40.43703333333333, 79.97533333333334]];
        yield ['40°26.222\' 79°58.52\'', [40.43703333333333, 79.97533333333334]];
        yield ['40.222° -79.5852°', [40.222, -79.5852]];
        yield ['40.222°, -79.5852°', [40.222, -79.5852]];
        yield ['44°58\'53.9"N 93°19\'25.8"W', [44.98163888888888888, -93.3238333333333334]];
        yield ['44°58\'53.9"N, 93°19\'25.8"W', [44.98163888888888888, -93.3238333333333334]];
        yield ['79:56:55W 40:26:46N', [-79.94861111111111, 40.44611111111111]];
        yield ['79:56:55 W, 40:26:46 N', [-79.94861111111111, 40.44611111111111]];
        yield ['79°56′55″W, 40°26′46″N', [-79.94861111111111, 40.44611111111111]];
    }

    /**
     * @dataProvider dataSourceBad
     *
     * @param class-string<ExceptionInterface> $exception
     */
    public function testBadValues(string|int|float $input, string $exception, string $message): void
    {
        self::expectException($exception);
        self::expectExceptionMessage($message);

        $parser = new Parser($input);

        $parser->parse();
    }

    /**
     * @param int|float|int[]|float[] $expected
     *
     * @dataProvider dataSourceGood
     */
    public function testGoodValues(string|int|float $input, $expected): void
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
}
