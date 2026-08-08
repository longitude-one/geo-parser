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
use LongitudeOne\Geo\String\Exception\LogicException;
use LongitudeOne\Geo\String\Lexer;
use LongitudeOne\Geo\String\TokenStream;
use PHPUnit\Framework\TestCase;

/**
 * Token stream tests.
 */
class TokenStreamTest extends TestCase
{
    /**
     * Token literals must be available for parser error messages.
     */
    public function testExposesTokenLiteral(): void
    {
        $stream = new TokenStream('40');

        self::assertSame('LongitudeOne\Geo\String\Lexer::T_FLOAT', $stream->literal(Lexer::T_FLOAT));
    }

    /**
     * The stream must expose the current token, glimpse ahead, and advance
     * without leaking the lexer's cursor mechanics to its caller.
     */
    public function testReadsAndConsumesTokens(): void
    {
        $stream = new TokenStream('40°N');

        self::assertTrue($stream->matches(Lexer::T_INTEGER));
        self::assertTrue($stream->matchesAny([Lexer::T_INTEGER, Lexer::T_FLOAT]));
        self::assertSame('40', $stream->current()?->value);
        self::assertSame(Lexer::T_DEGREE, $stream->glimpse()?->type);

        self::assertSame('40', $stream->consume(Lexer::T_INTEGER)->value);
        self::assertSame(Lexer::T_DEGREE, $stream->current()->type);
        self::assertSame('°', $stream->consume(Lexer::T_DEGREE)->value);
        self::assertSame(Lexer::T_CARDINAL_LAT, $stream->current()->type);
        self::assertSame('N', $stream->consume(Lexer::T_CARDINAL_LAT)->value);
        self::assertNull($stream->current());
    }

    /**
     * The stream must reject an attempt to consume a token of the wrong type.
     */
    public function testRejectsUnexpectedTokenConsumption(): void
    {
        $stream = new TokenStream('40');

        self::expectException(LogicException::class);
        self::expectExceptionMessage('Cannot consume token type '.Lexer::T_FLOAT.'.');

        $stream->consume(Lexer::T_FLOAT);
    }

    /**
     * A lexer that reports a matching token but exposes no consumed token must
     * raise a library exception that callers can catch through the interface.
     */
    public function testThrowsLibraryLogicExceptionWhenConsumedTokenIsMissing(): void
    {
        $lexer = $this->createStub(Lexer::class);
        $lexer->token = null;
        $lexer->method('isNextToken')->willReturn(true);

        $stream = new TokenStream('40', $lexer);

        try {
            $stream->consume(Lexer::T_INTEGER);
            self::fail('Expected a LogicException to be thrown.');
        } catch (ExceptionInterface $exception) {
            self::assertInstanceOf(LogicException::class, $exception);
            self::assertSame('A consumed token must be available.', $exception->getMessage());
        }
    }
}
