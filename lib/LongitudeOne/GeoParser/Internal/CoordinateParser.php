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

namespace LongitudeOne\GeoParser\Internal;

use LongitudeOne\Core\Enum\AxisEnum;
use LongitudeOne\GeoParser\Coordinate;
use LongitudeOne\GeoParser\Exception\LogicException;
use LongitudeOne\GeoParser\Exception\RangeException;
use LongitudeOne\GeoParser\Exception\UnexpectedValueException;
use LongitudeOne\GeoParser\Lexer;
use LongitudeOne\GeoParser\Point;

/**
 * Parse one or two coordinate values from text input.
 *
 * @internal this parser supports the public Parser facade
 */
final class CoordinateParser
{
    /**
     * Maximum length of a value embedded in an exception message.
     */
    private const MAX_ERROR_VALUE_LENGTH = 100;

    /**
     * Whether an optional colon or degree symbol can still be matched.
     */
    private bool $canMatchSymbol;

    /**
     * Original input string.
     */
    private string $input;

    /**
     * Whether the parser is reading the first coordinate in a pair.
     */
    private bool $isFirstCoordinate;

    /**
     * Axis required for the next coordinate when the previous one has a cardinal direction.
     */
    private ?AxisEnum $nextAxis;

    /**
     * Expected symbol token for the next angle component.
     */
    private ?int $nextSymbol;

    /**
     * Token stream for text input.
     */
    private TokenStream $tokens;

    /**
     * Create a parser for a textual coordinate value.
     */
    public function __construct(string $input)
    {
        $this->input = $input;
    }

    /**
     * Parse the text input as a single coordinate or coordinate pair.
     *
     * @return float|int|array<int, int|float>
     */
    public function parse(): float|int|array
    {
        $this->nextAxis = null;
        $this->nextSymbol = null;
        $this->isFirstCoordinate = true;
        $this->canMatchSymbol = true;
        $this->tokens = new TokenStream($this->input);

        return $this->point();
    }

    /**
     * Parse the text input as a single coordinate or coordinate pair.
     */
    public function parseAsCoordinates(): Coordinate|Point
    {
        $this->nextAxis = null;
        $this->nextSymbol = null;
        $this->isFirstCoordinate = true;
        $this->canMatchSymbol = true;
        $this->tokens = new TokenStream($this->input);

        return $this->pointAsCoordinates();
    }

    /**
     * Match a cardinal direction, validate its axis range, and apply its sign.
     */
    private function cardinal(int|float $value): Coordinate
    {
        $axis = $this->nextAxis ?? match ($this->tokens->current()?->type) {
            Lexer::T_CARDINAL_LAT => AxisEnum::LATITUDE,
            Lexer::T_CARDINAL_LON => AxisEnum::LONGITUDE,
            default => throw new LogicException(sprintf('Token type %d is not a cardinal direction.', $this->tokens->current()?->type)),
        };
        $tokenType = $this->cardinalTokenType($axis);
        if (!$this->tokens->matches($tokenType)) {
            throw $this->syntaxError($this->tokens->literal($tokenType));
        }

        $cardinal = $this->tokens->consumeCardinal($tokenType);
        $this->nextAxis = $cardinal->axis()->other();

        if ($value > $axis->rangeLimit()) {
            throw new RangeException($this->input, match ($axis) {
                AxisEnum::LATITUDE => RangeException::LATITUDE_OUT_OF_RANGE, AxisEnum::LONGITUDE => RangeException::LONGITUDE_OUT_OF_RANGE,
            });
        }

        return new Coordinate($value * $cardinal->sign(), $axis);
    }

    /**
     * Return the cardinal token type expected for an axis.
     */
    private function cardinalTokenType(AxisEnum $axis): int
    {
        return match ($axis) {
            AxisEnum::LATITUDE => Lexer::T_CARDINAL_LAT,
            AxisEnum::LONGITUDE => Lexer::T_CARDINAL_LON,
        };
    }

    /**
     * Match and return a single coordinate value.
     */
    private function coordinate(): Coordinate
    {
        $sign = null;

        if (null === $this->nextAxis && $this->tokens->matchesAny([Lexer::T_PLUS, Lexer::T_MINUS])) {
            $sign = $this->sign();
        }

        $coordinate = $this->degrees();
        $hasCardinal = null === $sign
            && (null !== $this->nextAxis || ($this->isFirstCoordinate && $this->tokens->matchesAny([Lexer::T_CARDINAL_LAT, Lexer::T_CARDINAL_LON])));
        $this->isFirstCoordinate = false;

        if ($hasCardinal) {
            return $this->cardinal($coordinate);
        }

        $this->nextAxis = null;

        return new Coordinate(($sign ?? 1) * $coordinate);
    }

    /**
     * Match and return a degree value.
     */
    private function degrees(): float|int
    {
        $angleParser = new AngleParser(
            $this->tokens,
            $this->input,
            $this->nextSymbol,
            $this->canMatchSymbol,
            fn (string $expected): UnexpectedValueException => $this->syntaxError($expected),
        );

        $degrees = $angleParser->parse();
        $this->nextSymbol = $angleParser->nextSymbol();
        $this->canMatchSymbol = $angleParser->canMatchSymbol();

        return $degrees;
    }

    /**
     * Ensure the coordinate pair consumed every token from the input.
     */
    private function ensureEndOfInput(): void
    {
        if (null !== $this->tokens->current()) {
            throw $this->syntaxError('end of string');
        }
    }

    /**
     * Match a token and return its value.
     */
    private function match(int $token): string|int
    {
        if (!$this->tokens->matches($token)) {
            throw $this->syntaxError($this->tokens->literal($token));
        }

        return $this->tokens->consume($token)->value;
    }

    /**
     * Match and return a single value or a pair.
     *
     * @return float|int|array<int, int|float>
     */
    private function point(): float|int|array
    {
        $x = $this->coordinate();

        if (null === $this->tokens->current()) {
            return $x->getValue();
        }

        if ($this->tokens->matches(Lexer::T_COMMA)) {
            $this->match(Lexer::T_COMMA);
        }

        $y = $this->coordinate();

        $this->ensureEndOfInput();

        return [$x->getValue(), $y->getValue()];
    }

    /**
     * Match and return a coordinate or point retaining explicit axes.
     */
    private function pointAsCoordinates(): Coordinate|Point
    {
        $first = $this->coordinate();

        if (null === $this->tokens->current()) {
            return $first;
        }

        if ($this->tokens->matches(Lexer::T_COMMA)) {
            $this->match(Lexer::T_COMMA);
        }

        $second = $this->coordinate();

        $this->ensureEndOfInput();

        return new Point($first, $second);
    }

    /**
     * Strip control characters and truncate a value before embedding it in an error message.
     */
    private function sanitizeForError(string $value): string
    {
        $value = (string) preg_replace('/[\x00-\x1F\x7F]/', ' ', $value);

        if (strlen($value) > self::MAX_ERROR_VALUE_LENGTH) {
            return substr($value, 0, self::MAX_ERROR_VALUE_LENGTH).'...';
        }

        return $value;
    }

    /**
     * Match plus or minus sign and return its coefficient.
     */
    private function sign(): int
    {
        if ($this->tokens->matches(Lexer::T_PLUS)) {
            $this->match(Lexer::T_PLUS);

            return 1;
        }

        $this->match(Lexer::T_MINUS);

        return -1;
    }

    /**
     * Create an exception with a descriptive error message.
     */
    private function syntaxError(string $expected): UnexpectedValueException
    {
        $expected = sprintf('Expected %s, got', $expected);
        $token = $this->tokens->current();
        $found = null === $token ? 'end of string.' : sprintf('"%s"', $this->sanitizeForError((string) $token->value));

        $message = sprintf(
            '[Syntax Error] line 0, col %d: Error: %s %s in value "%s"',
            $token->position ?? -1,
            $expected,
            $found,
            $this->sanitizeForError($this->input)
        );

        return new UnexpectedValueException($message);
    }
}
