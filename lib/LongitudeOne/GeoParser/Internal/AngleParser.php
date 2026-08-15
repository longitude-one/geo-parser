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

use LongitudeOne\GeoParser\Exception\RangeException;
use LongitudeOne\GeoParser\Exception\UnexpectedValueException;
use LongitudeOne\GeoParser\Lexer;

/**
 * Parse a coordinate angle in decimal, DDM, or DMS notation.
 *
 * @internal this parser supports the public Parser implementation
 */
final class AngleParser
{
    /**
     * Whether an optional colon or degree symbol can still be matched.
     */
    private bool $canMatchSymbol;

    /**
     * Whether the current minute notation permits a following second component.
     */
    private bool $canReadSeconds = false;

    /**
     * Original text input used in range exception messages.
     */
    private string $input;

    /**
     * Expected symbol token for the next angle component.
     */
    private ?int $nextSymbol;

    /**
     * Factory for syntax errors that preserves the public error contract.
     *
     * @var \Closure(string): UnexpectedValueException
     */
    private \Closure $syntaxError;

    /**
     * Token stream read by the angle parser.
     */
    private TokenStream $tokens;

    /**
     * Create an angle parser with the current coordinate parsing state.
     *
     * @param \Closure(string): UnexpectedValueException $syntaxError factory for syntax errors
     */
    public function __construct(TokenStream $tokens, string $input, ?int $nextSymbol, bool $canMatchSymbol, \Closure $syntaxError)
    {
        $this->tokens = $tokens;
        $this->input = $input;
        $this->nextSymbol = $nextSymbol;
        $this->canMatchSymbol = $canMatchSymbol;
        $this->syntaxError = $syntaxError;
    }

    /**
     * Whether an optional colon or degree symbol can still be matched.
     */
    public function canMatchSymbol(): bool
    {
        return $this->canMatchSymbol;
    }

    /**
     * Return the expected symbol token for the next angle component.
     */
    public function nextSymbol(): ?int
    {
        return $this->nextSymbol;
    }

    /**
     * Parse and convert the next coordinate angle to decimal degrees.
     */
    public function parse(): int|float
    {
        if (Lexer::T_APOSTROPHE === $this->nextSymbol || Lexer::T_QUOTE === $this->nextSymbol) {
            $this->nextSymbol = Lexer::T_DEGREE;
        }

        if ($this->tokens->matches(Lexer::T_FLOAT)) {
            $degrees = (float) $this->match(Lexer::T_FLOAT);

            if ($this->tokens->matches(Lexer::T_DEGREE)) {
                $this->match(Lexer::T_DEGREE);
                $this->nextSymbol = Lexer::T_DEGREE;
            }

            return (new Angle($degrees))->toDecimalDegrees();
        }

        $degrees = (int) $this->number();

        if (null === $this->symbol() || $this->isSpaceSeparatedCoordinateStarting()) {
            return (new Angle($degrees))->toDecimalDegrees();
        }

        $minutes = $this->minutes();
        $seconds = $this->canReadSeconds ? $this->seconds() : null;

        return (new Angle($degrees, $minutes, $seconds))->toDecimalDegrees();
    }

    /**
     * Determine whether the current position starts a coordinate separated by a space.
     */
    private function isSpaceSeparatedCoordinateStarting(): bool
    {
        $glimpse = $this->tokens->glimpse();

        return Lexer::T_COLON !== $this->nextSymbol
            && $this->tokens->matchesAny([Lexer::T_INTEGER, Lexer::T_FLOAT])
            && isset($glimpse->type)
            && Lexer::T_DEGREE === $glimpse->type;
    }

    /**
     * Consume a token or create the parser's existing syntax error.
     */
    private function match(int $token): string|int
    {
        if (!$this->tokens->matches($token)) {
            throw ($this->syntaxError)($this->tokens->literal($token));
        }

        return $this->tokens->consume($token)->value;
    }

    /**
     * Read and validate the minute component.
     */
    private function minutes(): float
    {
        if (Lexer::T_COLON === $this->nextSymbol || $this->tokens->matches(Lexer::T_INTEGER)) {
            $minutes = (float) (int) $this->match(Lexer::T_INTEGER);

            if ($minutes >= 60) {
                throw new RangeException($this->input, RangeException::MINUTES_OUT_OF_RANGE);
            }

            if (Lexer::T_COLON === $this->nextSymbol && !$this->tokens->matches(Lexer::T_COLON)) {
                return $minutes;
            }

            $this->symbol();
            $this->canReadSeconds = true;

            return $minutes;
        }

        if ($this->tokens->matches(Lexer::T_FLOAT)) {
            $minutes = (float) $this->match(Lexer::T_FLOAT);

            if ($minutes >= 60) {
                throw new RangeException($this->input, RangeException::MINUTES_OUT_OF_RANGE);
            }

            $this->symbol();

            return $minutes;
        }

        return 0.0;
    }

    /**
     * Read an integer or float token.
     */
    private function number(): int|string
    {
        if ($this->tokens->matches(Lexer::T_FLOAT)) {
            return $this->match(Lexer::T_FLOAT);
        }

        if ($this->tokens->matches(Lexer::T_INTEGER)) {
            return $this->match(Lexer::T_INTEGER);
        }

        throw ($this->syntaxError)('LongitudeOne\\GeoParser\\Lexer::T_INTEGER or LongitudeOne\\GeoParser\\Lexer::T_FLOAT');
    }

    /**
     * Read and validate the optional second component.
     */
    private function seconds(): float
    {
        if (!$this->tokens->matchesAny([Lexer::T_INTEGER, Lexer::T_FLOAT])) {
            return 0.0;
        }

        $seconds = (float) $this->number();

        if ($seconds >= 60) {
            throw new RangeException($this->input, RangeException::SECONDS_OUT_OF_RANGE);
        }

        if (Lexer::T_COLON !== $this->nextSymbol) {
            $this->symbol();
        }

        return $seconds;
    }

    /**
     * Match a component separator and update the expected next separator.
     */
    private function symbol(): ?int
    {
        if ($this->canMatchSymbol && null === $this->nextSymbol && $this->tokens->matches(Lexer::T_COLON)) {
            $this->match(Lexer::T_COLON);

            return $this->nextSymbol = Lexer::T_COLON;
        }

        if ($this->canMatchSymbol && null === $this->nextSymbol && $this->tokens->matches(Lexer::T_DEGREE)) {
            $this->match(Lexer::T_DEGREE);

            return $this->nextSymbol = Lexer::T_APOSTROPHE;
        }

        $symbol = $this->nextSymbol;
        $nextSymbol = match ($symbol) {
            Lexer::T_COLON => Lexer::T_COLON,
            Lexer::T_DEGREE => Lexer::T_APOSTROPHE,
            Lexer::T_APOSTROPHE => Lexer::T_QUOTE,
            Lexer::T_QUOTE => Lexer::T_QUOTE,
            default => null,
        };

        if (null !== $nextSymbol) {
            $this->match($symbol);

            return $this->nextSymbol = $nextSymbol;
        }

        $this->canMatchSymbol = false;

        return $this->nextSymbol = null;
    }
}
