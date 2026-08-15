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
}
