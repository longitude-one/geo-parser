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
     * Number of deterministic fuzz cases exercised by each public API.
     */
    private const FUZZ_CASES = 256;
    /**
     * ASCII characters mixed by the deterministic fuzz generator.
     *
     * The set includes coordinate syntax, cardinals, whitespace, controls,
     * and invalid separators without generating malformed UTF-8 sequences.
     */
    private const FUZZ_CHARACTERS = "0123456789+-.:,'\"NSEW \t\r\n#\0";

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
     * Generate a reproducible sequence of mixed coordinate-like input.
     */
    private static function fuzzInput(int $case): string
    {
        $input = '';
        $length = 1 + $case % 96;

        for ($position = 0; $position < $length; ++$position) {
            $byte = hash('sha256', sprintf('%d:%d', $case, $position), true)[0];
            $input .= self::FUZZ_CHARACTERS[ord($byte) % strlen(self::FUZZ_CHARACTERS)];
        }

        return $input;
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
     * Deterministic fuzzing protects the public APIs from parser-state bugs
     * without introducing the flakiness of an unseeded random test. Inputs
     * mix valid-looking fragments, controls, and invalid separators. Each
     * call may return a value or throw a library exception, but must never
     * expose a native PHP error or retain state from a preceding input.
     */
    public function testGeneratedInputsOnlyReturnOrThrowLibraryExceptions(): void
    {
        $parser = new Parser();
        $start = microtime(true);

        for ($case = 0; $case < self::FUZZ_CASES; ++$case) {
            $input = self::fuzzInput($case);

            $this->assertFuzzInputIsHandledByPublicApis($parser, $input);
        }

        self::assertLessThan(2.0, microtime(true) - $start, 'Deterministic fuzz inputs took too long to parse.');
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
    public function testParseWithoutInputAsCoordinateThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('An input value must be provided to either the constructor or the parseAsCoordinates method.');

        (new Parser())->parseAsCoordinates();
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
    public function testVeryLongInvalidInputStillFailsFastForBothPublicApis(): void
    {
        $input = str_repeat('x', 200000).'40';
        $start = microtime(true);

        $this->assertVeryLongInvalidInputIsRejected($input, false);
        $this->assertVeryLongInvalidInputIsRejected($input, true);

        self::assertLessThan(2.0, microtime(true) - $start, 'Parsing a long invalid input took too long.');
    }

    /**
     * Assert that both public APIs reject malformed fuzz input only through
     * the library's documented exception hierarchy.
     */
    private function assertFuzzInputIsHandledByPublicApis(Parser $parser, string $input): void
    {
        try {
            $parser->parse($input);
        } catch (ExceptionInterface) {
            self::addToAssertionCount(1);
        } catch (\Throwable $exception) {
            self::fail(sprintf('Parser::parse() threw %s for fuzz input %s.', $exception::class, bin2hex($input)));
        }

        try {
            $parser->parseAsCoordinates($input);
        } catch (ExceptionInterface) {
            self::addToAssertionCount(1);
        } catch (\Throwable $exception) {
            self::fail(sprintf('Parser::parseAsCoordinates() threw %s for fuzz input %s.', $exception::class, bin2hex($input)));
        }
    }

    /**
     * Assert that a very large malformed input is rejected by one public API.
     */
    private function assertVeryLongInvalidInputIsRejected(string $input, bool $asCoordinates): void
    {
        try {
            if ($asCoordinates) {
                (new Parser($input))->parseAsCoordinates();
            } else {
                (new Parser($input))->parse();
            }

            self::fail('Expected an UnexpectedValueException to be thrown.');
        } catch (UnexpectedValueException) {
            self::addToAssertionCount(1);
        }
    }
}
