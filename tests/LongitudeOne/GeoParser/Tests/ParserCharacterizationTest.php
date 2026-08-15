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

use LongitudeOne\GeoParser\Exception\RangeException;
use LongitudeOne\GeoParser\Exception\UnexpectedValueException;
use LongitudeOne\GeoParser\Parser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Characterization tests for the Parser's public behavior.
 *
 * These tests intentionally preserve the current values and numeric types
 * before the parsing internals are refactored.
 */
class ParserCharacterizationTest extends TestCase
{
    /**
     * Provide representative coordinate formats and their current results.
     *
     * @return \Generator<string, array{string, int|float|array<int, int|float>}, null, void>
     */
    public static function dataSourceCoordinateFormats(): \Generator
    {
        // Decimal degrees and scientific notation.
        yield 'integer decimal degrees' => ['40', 40];
        yield 'float decimal degrees' => ['40.0', 40.0];
        yield 'scientific notation' => ['1e5', 100000.0];

        // Degree notation currently promotes an integer degree to a float.
        yield 'integer degrees with degree symbol' => ['40°', 40.0];
        yield 'integer degrees with cardinal direction' => ['40°S', -40.0];

        // Degrees, minutes, and seconds notation.
        yield 'degrees and minutes' => ["40°30'", 40.5];
        yield 'degrees, minutes, and seconds' => ['40°30\'0"', 40.5];
        yield 'colon-separated degrees and minutes' => ['40:30', 40.5];
        yield 'colon-separated degrees, minutes, and seconds' => ['40:30:0', 40.5];

        // Coordinate pairs with plain, degree, and cardinal notation.
        yield 'space-separated integer pair' => ['40 79', [40, 79]];
        yield 'comma-separated degree pair' => ['40°, 79°', [40.0, 79.0]];
        yield 'cardinal coordinate pair' => ['40°N, 79°W', [40.0, -79.0]];
    }

    /**
     * Provide invalid inputs whose exception class and message are part of the
     * current public behavior.
     *
     * @return \Generator<string, array{string, class-string<\Throwable>, string}, null, void>
     */
    public static function dataSourceInvalidCoordinates(): \Generator
    {
        yield 'explicit sign with cardinal direction' => [
            '-40 S',
            UnexpectedValueException::class,
            '[Syntax Error] line 0, col 4: Error: Expected LongitudeOne\\GeoParser\\Lexer::T_INTEGER or LongitudeOne\\GeoParser\\Lexer::T_FLOAT, got "S" in value "-40 S"',
        ];
        yield 'unexpected sign after cardinal' => [
            '40°N +45°W',
            UnexpectedValueException::class,
            '[Syntax Error] line 0, col 6: Error: Expected LongitudeOne\GeoParser\Lexer::T_INTEGER or LongitudeOne\GeoParser\Lexer::T_FLOAT, got "+" in value "40°N +45°W"',
        ];
        yield 'latitude out of range' => [
            '100N',
            RangeException::class,
            '[RangeException] Latitude must be between -90 and 90, got "100N".',
        ];
        yield 'minutes out of range' => [
            '55:60:32',
            RangeException::class,
            '[RangeException] Minutes must be between 0 and 59, got "55:60:32".',
        ];
        yield 'trailing token' => [
            '40 45W',
            UnexpectedValueException::class,
            '[Syntax Error] line 0, col 5: Error: Expected end of string, got "W" in value "40 45W"',
        ];
    }

    /**
     * Constructor input must retain the parser's current value and type for
     * each supported text format.
     *
     * @param string                          $input    input passed to the constructor
     * @param int|float|array<int, int|float> $expected current parsed value and type
     */
    #[DataProvider('dataSourceCoordinateFormats')]
    public function testConstructorInputMatchesCurrentBehavior(string $input, int|float|array $expected): void
    {
        self::assertSame($expected, (new Parser($input))->parse());
    }

    /**
     * Invalid input must continue to expose the same exception contract.
     *
     * @param string                   $input     invalid coordinate input
     * @param class-string<\Throwable> $exception expected exception class
     * @param string                   $message   expected exception message
     */
    #[DataProvider('dataSourceInvalidCoordinates')]
    public function testInvalidInputMatchesCurrentExceptionContract(string $input, string $exception, string $message): void
    {
        self::expectException($exception);
        self::expectExceptionMessage($message);

        (new Parser($input))->parse();
    }

    /**
     * Parse method input must retain the parser's current value and type for
     * each supported text format.
     *
     * @param string                          $input    input passed to the parse method
     * @param int|float|array<int, int|float> $expected current parsed value and type
     */
    #[DataProvider('dataSourceCoordinateFormats')]
    public function testParseMethodInputMatchesCurrentBehavior(string $input, int|float|array $expected): void
    {
        $parser = new Parser();

        self::assertSame($expected, $parser->parse($input));
    }

    /**
     * A single parser instance must preserve the current behavior while its
     * input changes between all supported text formats.
     */
    public function testParserReuseMatchesCurrentBehavior(): void
    {
        $parser = new Parser();

        foreach (self::dataSourceCoordinateFormats() as [$input, $expected]) {
            self::assertSame($expected, $parser->parse($input));
        }
    }
}
