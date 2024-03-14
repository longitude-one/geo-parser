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
     * @return array<string,string>[]
     */
    public static function dataSourceBad(): array
    {
        return [
            [
                'input' => '-40°N 45°W',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col 5: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "N" in value "-40°N 45°W"',
            ],
            [
                'input' => '+40°N 45°W',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col 5: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "N" in value "+40°N 45°W"',
            ],
            [
                'input' => '40°N +45°W',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col 6: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "+" in value "40°N +45°W"',
            ],
            [
                'input' => '40°N -45W',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col 6: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "-" in value "40°N -45W"',
            ],
            [
                'input' => '40N -45°W',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col 4: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "-" in value "40N -45°W"',
            ],
            [
                'input' => '40N 45°W',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col 6: Error: Expected LongitudeOne\Geo\String\Lexer::T_CARDINAL_LON, got "°" in value "40N 45°W"',
            ],
            [
                'input' => '40°N 45°S',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col 10: Error: Expected LongitudeOne\Geo\String\Lexer::T_CARDINAL_LON, got "S" in value "40°N 45°S"',
            ],
            [
                'input' => '40°W 45°E',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col 10: Error: Expected LongitudeOne\Geo\String\Lexer::T_CARDINAL_LAT, got "E" in value "40°W 45°E"',
            ],
            [
                'input' => '40° 45',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col -1: Error: Expected LongitudeOne\Geo\String\Lexer::T_APOSTROPHE, got end of string. in value "40° 45"',
            ],
            [
                'input' => '40°, 45',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col -1: Error: Expected LongitudeOne\Geo\String\Lexer::T_DEGREE, got end of string. in value "40°, 45"',
            ],
            [
                'input' => '40N 45',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col -1: Error: Expected LongitudeOne\Geo\String\Lexer::T_CARDINAL_LON, got end of string. in value "40N 45"',
            ],
            [
                'input' => '40 45W',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col 5: Error: Expected end of string, got "W" in value "40 45W"',
            ],
            [
                'input' => '-40.757° 45°W',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col 14: Error: Expected end of string, got "W" in value "-40.757° 45°W"',
            ],
            [
                'input' => '40.757°N -45.567°W',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col 10: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "-" in value "40.757°N -45.567°W"',
            ],
            [
                'input' => '44°58\'53.9N 93°19\'25.8"W',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col 11: Error: Expected LongitudeOne\Geo\String\Lexer::T_QUOTE, got "N" in value "44°58\'53.9N 93°19\'25.8"W"',
            ],
            [
                'input' => '40:26\'',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col 5: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "\'" in value "40:26\'"',
            ],
            [
                'input' => '132.4432:',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col 8: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got ":" in value "132.4432:"',
            ],
            [
                'input' => '55:34:22°',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col 8: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "°" in value "55:34:22°"',
            ],
            [
                'input' => '55:34.22',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col 3: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER, got "34.22" in value "55:34.22"',
            ],
            [
                'input' => '55#34.22',
                'exception' => UnexpectedValueException::class,
                'message' => '[Syntax Error] line 0, col 2: Error: Expected LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT, got "#" in value "55#34.22"',
            ],
            [
                'input' => '200N',
                'exception' => RangeException::class,
                'message' => '[Range Error] Error: Degrees out of range -90 to 90 in value "200N"',
            ],
            [
                'input' => '55:200:32',
                'exception' => RangeException::class,
                'message' => '[Range Error] Error: Minutes greater than 60 in value "55:200:32"',
            ],
            [
                'input' => '55:20:99',
                'exception' => RangeException::class,
                'message' => '[Range Error] Error: Seconds greater than 60 in value "55:20:99"',
            ],
            [
                'input' => '55°70.99\'',
                'exception' => RangeException::class,
                'message' => '[Range Error] Error: Minutes greater than 60 in value "55°70.99\'"',
            ],
        ];
    }

    /**
     * @return array<int, array<string, array<int,float|int>|float|int|string>>
     */
    public static function dataSourceGood(): array
    {
        return [
            [
                'input' => 40,
                'expected' => 40,
            ],
            [
                'input' => '40',
                'expected' => 40,
            ],
            [
                'input' => '-40',
                'expected' => -40,
            ],
            [
                'input' => '1E5',
                'expected' => 100000,
            ],
            [
                'input' => '1e5',
                'expected' => 100000,
            ],
            [
                'input' => '1e5°',
                'expected' => 100000,
            ],
            [
                'input' => '40°',
                'expected' => 40,
            ],
            [
                'input' => '-40°',
                'expected' => -40,
            ],
            [
                'input' => '40° N',
                'expected' => 40,
            ],
            [
                'input' => '40° S',
                'expected' => -40,
            ],
            [
                'input' => '45.24',
                'expected' => 45.24,
            ],
            [
                'input' => 45.24,
                'expected' => 45.24,
            ],
            [
                'input' => '45.24°',
                'expected' => 45.24,
            ],
            [
                'input' => '+45.24°',
                'expected' => 45.24,
            ],
            [
                'input' => '45.24° S',
                'expected' => -45.24,
            ],
            [
                'input' => '40° 26\' 46" N',
                'expected' => 40.44611111111111,
            ],
            [
                'input' => '40:26S',
                'expected' => -40.4333333333333333,
            ],
            [
                'input' => '79:56:55W',
                'expected' => -79.94861111111111,
            ],
            [
                'input' => '40:26:46N',
                'expected' => 40.44611111111111,
            ],
            [
                'input' => '40° N 79° W',
                'expected' => [40, -79],
            ],
            [
                'input' => '40 79',
                'expected' => [40, 79],
            ],
            [
                'input' => '40° 79°',
                'expected' => [40, 79],
            ],
            [
                'input' => '40, 79',
                'expected' => [40, 79],
            ],
            [
                'input' => '40°, 79°',
                'expected' => [40, 79],
            ],
            [
                'input' => '40° 26\' 46" N 79° 58\' 56" W',
                'expected' => [40.44611111111111, -79.98222222222222],
            ],
            [
                'input' => '40° 26\' N 79° 58\' W',
                'expected' => [40.4333333333333333, -79.96666666666666669],
            ],
            [
                'input' => '40.4738° N, 79.553° W',
                'expected' => [40.4738, -79.553],
            ],
            [
                'input' => '40.4738° S, 79.553° W',
                'expected' => [-40.4738, -79.553],
            ],
            [
                'input' => '40° 26.222\' N 79° 58.52\' E',
                'expected' => [40.43703333333333, 79.97533333333334],
            ],
            [
                'input' => '40°26.222\'N 79°58.52\'E',
                'expected' => [40.43703333333333, 79.97533333333334],
            ],
            [
                'input' => '40°26.222\' 79°58.52\'',
                'expected' => [40.43703333333333, 79.97533333333334],
            ],
            [
                'input' => '40.222° -79.5852°',
                'expected' => [40.222, -79.5852],
            ],
            [
                'input' => '40.222°, -79.5852°',
                'expected' => [40.222, -79.5852],
            ],
            [
                'input' => '44°58\'53.9"N 93°19\'25.8"W',
                'expected' => [44.98163888888888888, -93.3238333333333334],
            ],
            [
                'input' => '44°58\'53.9"N, 93°19\'25.8"W',
                'expected' => [44.98163888888888888, -93.3238333333333334],
            ],
            [
                'input' => '79:56:55W 40:26:46N',
                'expected' => [-79.94861111111111, 40.44611111111111],
            ],
            [
                'input' => '79:56:55 W, 40:26:46 N',
                'expected' => [-79.94861111111111, 40.44611111111111],
            ],
            [
                'input' => '79°56′55″W, 40°26′46″N',
                'expected' => [-79.94861111111111, 40.44611111111111],
            ],
        ];
    }

    /**
     * @dataProvider dataSourceBad
     *
     * @param class-string<\Throwable> $exception
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
            $input = $data['input'];
            $expected = $data['expected'];

            $value = $parser->parse($input);

            self::assertEquals($expected, $value);
        }
    }
}
