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

use LongitudeOne\Geo\String\Exception\ExceptionInterface;
use LongitudeOne\Geo\String\Exception\RangeException;
use LongitudeOne\Geo\String\Exception\UnexpectedValueException;
use LongitudeOne\Geo\String\Parser;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;

/**
 * Invalid coordinate syntax and range validation.
 *
 * Parser errors are part of the library's public contract: consumers may
 * report their class and message to users or logs. Both public parsing APIs
 * must therefore reject malformed and out-of-range input consistently.
 */
class ParserInvalidInputTest extends TestCase
{
    /**
     * Structured parsing shares the same grammar rather than introducing a
     * second validation path. Keep its failure contract in lockstep with
     * Parser::parse() as the implementation evolves.
     *
     * @param class-string<ExceptionInterface> $exception
     */
    #[DataProviderExternal(ParserDataProvider::class, 'dataSourceBad')]
    public function testParseAsCoordinatesRejectsInvalidInput(string $input, string $exception, string $message): void
    {
        self::expectException($exception);
        self::expectExceptionMessage($message);

        (new Parser($input))->parseAsCoordinates();
    }

    /**
     * The legacy API must preserve the expected library exception for each
     * invalid grammar, cardinal-axis mismatch, and geographic range error.
     *
     * @param class-string<ExceptionInterface> $exception
     */
    #[DataProviderExternal(ParserDataProvider::class, 'dataSourceBad')]
    public function testParseRejectsInvalidInput(string $input, string $exception, string $message): void
    {
        self::expectException($exception);
        self::expectExceptionMessage($message);

        (new Parser($input))->parse();
    }

    /**
     * Latitude has a stricter geographic bound than longitude. This named
     * check keeps that user-facing rule visible outside the range matrix.
     */
    public function testRejectsLatitudeOutsideGeographicRange(): void
    {
        self::expectException(RangeException::class);
        self::expectExceptionMessage('Latitude must be between -90 and 90');

        (new Parser('100N'))->parseAsCoordinates();
    }

    /**
     * A point whose second cardinal repeats the latitude axis is ambiguous.
     * The parser must demand longitude rather than accepting a superficially
     * valid pair of cardinal directions.
     */
    public function testRejectsPointWithTwoLatitudeCardinals(): void
    {
        self::expectException(UnexpectedValueException::class);
        self::expectExceptionMessage('Expected LongitudeOne\\Geo\\String\\Lexer::T_CARDINAL_LON');

        (new Parser('40°N 45°S'))->parse();
    }
}
