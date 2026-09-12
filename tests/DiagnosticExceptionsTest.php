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

use LongitudeOne\Core\Diagnostic\DiagnosticValueFormatter;
use LongitudeOne\GeoParser\Exception\ExceptionInterface;
use LongitudeOne\GeoParser\Exception\RangeException;
use LongitudeOne\GeoParser\Internal\Cardinal;
use LongitudeOne\GeoParser\Parser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Untrusted exception values use the shared spatial-core diagnostic formatter.
 */
final class DiagnosticExceptionsTest extends TestCase
{
    /**
     * @return \Generator<string, array{string}, null, void>
     */
    public static function diagnosticValues(): \Generator
    {
        yield 'controls' => ["\0\r\n\t\x1B\x7F"];
        yield 'unicode controls' => ["\u{2028}\u{2029}\u{202E}\u{200B}"];
        yield 'invalid utf8' => ["\xFF\xC3("];
        yield 'long unicode' => [str_repeat('é', 3000)];
    }

    #[DataProvider('diagnosticValues')]
    public function testInvalidCardinalFormatsToken(string $value): void
    {
        try {
            Cardinal::fromToken($value);
            self::fail('Expected a library exception.');
        } catch (ExceptionInterface $exception) {
            self::assertSame('Token "'.DiagnosticValueFormatter::format($value).'" is not a cardinal direction.', $exception->getMessage());
        }
    }

    #[DataProvider('diagnosticValues')]
    public function testParserFormatsEveryUntrustedValue(string $value): void
    {
        foreach (['x', '100N', '-40N', '40:60', '40:30:60'] as $prefix) {
            $input = $prefix.$value;
            foreach (['parse', 'parseAsCoordinates'] as $method) {
                try {
                    (new Parser($input))->{$method}();
                    self::fail('Expected a library exception.');
                } catch (ExceptionInterface $exception) {
                    self::assertStringContainsString(DiagnosticValueFormatter::format($input), $exception->getMessage());
                    self::assertSame(0, preg_match('/[\p{Cc}\p{Cf}\p{Zl}\p{Zp}]/u', $exception->getMessage()));
                }
            }
        }
    }

    #[DataProvider('diagnosticValues')]
    public function testRangeExceptionFormatsValueAndPreservesCause(string $value): void
    {
        $previous = new \RuntimeException('Previous failure.');
        $exception = new RangeException($value, RangeException::LATITUDE_OUT_OF_RANGE, $previous);

        self::assertSame('[RangeException] Latitude must be between -90 and 90, got "'.DiagnosticValueFormatter::format($value).'".', $exception->getMessage());
        self::assertSame(RangeException::LATITUDE_OUT_OF_RANGE, $exception->getCode());
        self::assertSame($previous, $exception->getPrevious());
    }
}
