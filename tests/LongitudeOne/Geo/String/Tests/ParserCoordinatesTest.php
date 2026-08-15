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
use LongitudeOne\Geo\String\Parser;
use LongitudeOne\Geo\String\Point;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Structured coordinate results returned by Parser::parseAsCoordinates().
 *
 * This suite protects the Version 4 API. A single input must return a
 * Coordinate and a pair a Point; their values must match normal parsing. Most
 * importantly, an axis is attached only to an explicit cardinal direction and
 * remains null for signed or positional coordinates that do not name one.
 */
class ParserCoordinatesTest extends TestCase
{
    /**
     * @return \Generator<int, array{string|int, Coordinate|Point}, null, void>
     */
    public static function dataSourceStructuredCoordinates(): \Generator
    {
        foreach (ParserDataProvider::dataSourceGood() as [$input, , $expectedCoordinates]) {
            yield [$input, $expectedCoordinates];
        }
    }

    /**
     * Cardinal directions carry semantic axis information. Keep this named
     * scenario separate from the broad format matrix because losing either
     * axis would silently degrade the Version 4 structured API.
     */
    public function testKeepsAxesForCanonicalLongitudeLatitudePoint(): void
    {
        $result = (new Parser('79°56′55″W, 40°26′46″N'))->parseAsCoordinates();

        self::assertInstanceOf(Point::class, $result);
        self::assertSame(AxisEnum::LONGITUDE, $result->getFirst()->getAxis());
        self::assertSame(AxisEnum::LATITUDE, $result->getSecond()->getAxis());
    }

    /**
     * Numeric position alone never identifies a geographic axis. This guards
     * against a tempting but incorrect inference of latitude then longitude
     * when callers pass a plain coordinate pair.
     */
    public function testLeavesAxesNullForPointWithoutCardinals(): void
    {
        $result = (new Parser('40.222°, -79.5852°'))->parseAsCoordinates();

        self::assertInstanceOf(Point::class, $result);
        self::assertNull($result->getFirst()->getAxis());
        self::assertNull($result->getSecond()->getAxis());
    }

    /**
     * Every accepted format is checked against an explicit structured result.
     * This prevents a change in token consumption from losing a cardinal axis,
     * or from inventing one where the grammar gives no such information.
     */
    #[DataProvider('dataSourceStructuredCoordinates')]
    public function testParseAsCoordinatesReturnsExpectedStructure(string|int $input, Coordinate|Point $expectedCoordinates): void
    {
        self::assertEquals($expectedCoordinates, (new Parser())->parseAsCoordinates($input));
    }

    /**
     * The structured API must also be safe to reuse. This catches stale axis,
     * separator, or token-stream state when a parser handles multiple values.
     */
    public function testReusedParserReturnsExpectedCoordinateStructure(): void
    {
        $parser = new Parser();

        foreach (self::dataSourceStructuredCoordinates() as [$input, $expectedCoordinates]) {
            self::assertEquals($expectedCoordinates, $parser->parseAsCoordinates($input));
        }
    }
}
