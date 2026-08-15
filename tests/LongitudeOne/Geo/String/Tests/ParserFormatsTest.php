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

use LongitudeOne\Geo\String\Parser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;

/**
 * Accepted coordinate grammar and legacy numeric results.
 *
 * These tests are the compatibility boundary for Parser::parse(). They verify
 * every supported notation still produces the scalar or numeric pair expected
 * by callers, independently of the richer Coordinate and Point API.
 */
class ParserFormatsTest extends TestCase
{
    /**
     * Reuse the canonical examples while intentionally discarding their typed
     * expectation: this suite owns only the legacy numeric return contract.
     *
     * @return \Generator<int, array{string|int, int|float|array<int, int|float>}, null, void>
     */
    public static function dataSourceSupportedValues(): \Generator
    {
        foreach (ParserDataProvider::dataSourceGood() as [$input, $expected]) {
            yield [$input, $expected];
        }
    }

    /**
     * Both constructor input and method input are supported public entry
     * points. This test exercises the latter across the full accepted grammar.
     *
     * @param int|float|array<int, int|float> $expected
     */
    #[DataProvider('dataSourceSupportedValues')]
    public function testAcceptedSyntaxReturnsExpectedNumericValue(string|int $input, int|float|array $expected): void
    {
        self::assertEquals($expected, (new Parser())->parse($input));
    }

    /**
     * Documentation examples are exercised with all supported pair separators
     * so prose examples remain executable compatibility tests.
     *
     * @param array{0: float|int|string, 1: float|int|string} $coordinates
     * @param array{0: float|int, 1: float|int}               $expected
     */
    #[DataProviderExternal(ParserDataProvider::class, 'dataSourceFromDocumentation')]
    public function testDocumentationPairsAcceptEverySeparator(array $coordinates, array $expected): void
    {
        foreach ([' ', ',', ' ,', ', ', ' , '] as $separator) {
            self::assertEquals($expected, (new Parser(implode($separator, $coordinates)))->parse());
        }
    }

    /**
     * This well-known longitude/latitude pair is a readable regression test
     * for the documented DMS syntax, cardinal signs, and pair ordering.
     */
    public function testParsesCanonicalWestLongitudeAndNorthLatitudePair(): void
    {
        self::assertEquals(
            [-79.94861111111111, 40.44611111111111],
            (new Parser('79°56′55″W, 40°26′46″N'))->parse(),
        );
    }

    /**
     * Reusing a Parser is documented behavior. Each new input must fully
     * replace the previous state, regardless of notation or return shape.
     */
    public function testReusedParserAcceptsEverySupportedFormat(): void
    {
        $parser = new Parser();

        foreach (self::dataSourceSupportedValues() as [$input, $expected]) {
            self::assertEquals($expected, $parser->parse($input));
        }
    }
}
