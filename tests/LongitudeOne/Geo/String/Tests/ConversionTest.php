<?php

/**
 * This file is part of the LongitudeOne GeoParser project.
 *
 * PHP 8.3 | 8.4 | 8.5
 *
 * Copyright LongitudeOne - Alexandre Tranchant - Derek J. Lambert.
 * Copyright 2024-2026.
 */

namespace LongitudeOne\Geo\String\Tests;

use LongitudeOne\Geo\String\Parser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Numeric conversion tests for the Parser.
 */
class ConversionTest extends TestCase
{
    /**
     * Provide native numeric values and numeric strings with their expected
     * parsed values and types.
     *
     * @return \Generator<string, array{string|int|float, int|float}, null, void>
     */
    public static function dataSource(): \Generator
    {
        // Native integer and float values.
        yield 'integer' => [20, 20];
        yield 'float' => [20.0, 20.0];
        yield 'float with decimal part' => [20.5, 20.5];

        // Floats already expressed using scientific notation.
        yield 'float in scientific notation' => [1.0E+20, 1.0E+20];
        yield 'small float in scientific notation' => [1e-5, 1e-5];

        // Numeric strings must retain the type expressed by their notation.
        yield 'integer string' => ['20', 20];
        yield 'negative integer string' => ['-20', -20];
        yield 'float string ending in zero' => ['20.0', 20.0];
        yield 'negative float string ending in zero' => ['-20.0', -20.0];
    }

    /**
     * Constructor input must preserve its numeric type: integers must not
     * become floats, while values written as floats must remain floats.
     *
     * @param string|int|float $input    input value passed to the constructor
     * @param int|float        $expected expected parsed value and type
     */
    #[DataProvider('dataSource')]
    public function testConversionPreservesConstructorType(string|int|float $input, int|float $expected): void
    {
        $value = (new Parser($input))->parse();

        self::assertSame($expected, $value);
    }

    /**
     * Parse method input must preserve its numeric type: integers must not
     * become floats, while values written as floats must remain floats.
     *
     * @param string|int|float $input    input value passed to the parse method
     * @param int|float        $expected expected parsed value and type
     */
    #[DataProvider('dataSource')]
    public function testConversionPreservesParameterType(string|int|float $input, int|float $expected): void
    {
        $value = (new Parser())->parse($input);

        self::assertSame($expected, $value);
    }

    /**
     * Reusing a parser must not retain the previous string or numeric input.
     */
    public function testParserReuseDoesNotRetainPreviousInputType(): void
    {
        $parser = new Parser();

        self::assertSame(20, $parser->parse(20));
        self::assertSame(20.0, $parser->parse('20.0'));
        self::assertSame(-20.0, $parser->parse(-20.0));
        self::assertSame(-20, $parser->parse('-20'));
    }
}
