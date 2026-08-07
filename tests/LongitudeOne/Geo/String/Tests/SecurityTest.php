<?php

/**
 * This file is part of the LongitudeOne GeoParser project.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright LongitudeOne - Alexandre Tranchant - Derek J. Lambert.
 * Copyright 2024-2026.
 *
 */

namespace LongitudeOne\Geo\String\Tests;

use LongitudeOne\Geo\String\Exception\InvalidArgumentException;
use LongitudeOne\Geo\String\Exception\RangeException;
use LongitudeOne\Geo\String\Exception\UnexpectedValueException;
use LongitudeOne\Geo\String\Parser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Security and robustness tests for the Parser.
 *
 * These tests cover edge cases that are not primarily about parsing correctness,
 * but about how the Parser behaves when fed empty, extreme, oversized or
 * otherwise hostile input.
 */
class SecurityTest extends TestCase
{
    /**
     * @return \Generator<string, array{string}, null, void>
     */
    public static function dataSourceEmptyOrBlank(): \Generator
    {
        yield 'empty string' => [''];
        yield 'single space' => [' '];
        yield 'only whitespace' => ["\t\n\r "];
    }

    /**
     * @return \Generator<string, array{string}, null, void>
     */
    public static function dataSourceExtremeValues(): \Generator
    {
        yield 'huge positive exponent' => ['1e400', \INF];
        yield 'huge negative exponent' => ['-1e400', -\INF];
        yield 'huge digit string without cardinal' => ['999999999999999999999', 1.0E+21];
    }

    /**
     * An empty or blank input must fail cleanly with a syntax error, not a fatal
     * error, a warning, or a silent "empty" value.
     */
    #[DataProvider('dataSourceEmptyOrBlank')]
    public function testEmptyOrBlankInputThrowsSyntaxError(string $input): void
    {
        $this->expectException(UnexpectedValueException::class);

        (new Parser($input))->parse();
    }

    /**
     * The exception message must not contain raw newlines coming from the
     * input: logging this message as-is would allow an attacker to forge fake
     * log lines (log/CRLF injection).
     *
     * This test currently fails: the raw input, including any CR/LF, is
     * embedded verbatim in the exception message.
     */
    public function testExceptionMessageDoesNotContainRawNewlines(): void
    {
        $input = "40\r\nFAKE LOG LINE: admin logged in\r\nN";

        try {
            (new Parser($input))->parse();
            self::fail('Expected an UnexpectedValueException to be thrown.');
        } catch (UnexpectedValueException $exception) {
            self::assertStringNotContainsString("\r", $exception->getMessage(), 'Exception message must not contain a carriage return coming from the raw input, as this would allow log/CRLF injection.');
            self::assertStringNotContainsString("\n", $exception->getMessage(), 'Exception message must not contain a line feed coming from the raw input, as this would allow log/CRLF injection.');
        }
    }

    /**
     * The exception message must not grow proportionally to the size of the
     * attacker-controlled input: today the offending token and the full input
     * are both copied verbatim into the message, so a large invalid payload
     * produces a message of a similar size.
     *
     * This test currently fails: there is no bound on the message length.
     */
    public function testExceptionMessageLengthIsBounded(): void
    {
        $input = str_repeat('x', 100000);

        try {
            (new Parser($input))->parse();
            self::fail('Expected an UnexpectedValueException to be thrown.');
        } catch (UnexpectedValueException $exception) {
            self::assertLessThan(
                1000,
                strlen($exception->getMessage()),
                'Exception message length grows with the attacker-controlled input length.'
            );
        }
    }

    /**
     * Extreme numeric values (scientific notation overflowing to +/-INF, or huge
     * plain integers) are currently accepted and returned as-is when there is no
     * cardinal direction to range-check against. This test documents today's
     * behaviour; it is not necessarily the desired behaviour.
     */
    #[DataProvider('dataSourceExtremeValues')]
    public function testExtremeValueWithoutCardinalIsNotRangeChecked(string $input, float $expected): void
    {
        $value = (new Parser($input))->parse();

        self::assertSame($expected, $value);
    }

    /**
     * A null byte embedded in the input must not crash the lexer/parser; it
     * should simply be rejected as an unexpected token.
     */
    public function testNullByteInInputThrowsSyntaxError(): void
    {
        $this->expectException(UnexpectedValueException::class);

        (new Parser("40\0N"))->parse();
    }

    /**
     * A parser created without input must fail with the library exception,
     * rather than an error caused by an uninitialized property.
     */
    public function testParseWithoutInputThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('An input value must be provided to either the constructor or the parse method.');

        (new Parser())->parse();
    }

    /**
     * RangeException must truncate an overly long value instead of embedding it
     * in full, for the same reason as the Parser's syntax error message: an
     * attacker-controlled value must not be able to inflate the exception
     * message size.
     */
    public function testRangeExceptionTruncatesLongValue(): void
    {
        $value = str_repeat('9', 200);

        $exception = new RangeException($value, RangeException::LATITUDE_OUT_OF_RANGE);

        self::assertStringNotContainsString($value, $exception->getMessage());
        self::assertStringContainsString('...', $exception->getMessage());
        self::assertLessThan(200, strlen($exception->getMessage()));
    }

    /**
     * A repeated sign must not be silently collapsed or ignored.
     */
    public function testRepeatedSignIsRejected(): void
    {
        $this->expectException(UnexpectedValueException::class);

        (new Parser('--40'))->parse();
    }

    /**
     * A very large, invalid payload must not make the parser hang or blow up
     * memory disproportionately; it must still fail fast with a syntax error.
     */
    public function testVeryLongInvalidInputStillFailsFast(): void
    {
        $input = str_repeat('x', 200000).'40';

        $start = microtime(true);

        try {
            (new Parser($input))->parse();
            self::fail('Expected an UnexpectedValueException to be thrown.');
        } catch (UnexpectedValueException $exception) {
            $elapsed = microtime(true) - $start;
            self::assertLessThan(1.0, $elapsed, 'Parsing a long invalid input took too long.');
        }
    }
}
