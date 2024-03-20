<?php

/**
 * This file is part of the LongitudeOne GeoParser project.
 *
 * PHP 7.4
 *
 * Copyright LongitudeOne - Alexandre Tranchant - Derek J. Lambert.
 * Copyright 2024.
 *
 */

namespace LongitudeOne\Geo\String;

use Doctrine\Common\Lexer\Token;
use LongitudeOne\Geo\String\Exception\RangeException;
use LongitudeOne\Geo\String\Exception\UnexpectedValueException;

/**
 * Parser for geographic coordinate strings.
 */
class Parser
{
    /**
     * @var string original input string
     */
    private string $input;

    /**
     * @var Lexer lexer doctrine instance
     */
    private Lexer $lexer;

    /**
     * Cardinal direction can be Lexer::CARDINAL_LAT or Lexer::CARDINAL_LON.
     *
     * @var int|null next cardinal direction token type when present
     */
    private ?int $nextCardinal;

    /**
     * Symbol can be Lexer::T_APOSTROPHE, Lexer::T_QUOTE, or Lexer::T_DEGREE.
     *
     * @var int|false|null next symbol token type when present
     */
    private int|false|null $nextSymbol;

    /**
     * Constructor.
     *
     * Setup up instance properties
     */
    public function __construct(string|int|null $input = null)
    {
        $this->lexer = new Lexer();

        if (null !== $input) {
            $this->input = (string) $input;
        }
    }

    /**
     * Parse input string.
     *
     * @return float|int|array<int|float>
     */
    public function parse(string|int|null $input = null): float|int|array
    {
        if (null !== $input) {
            $this->input = (string) $input;
        }

        $this->nextCardinal = null;
        $this->nextSymbol = null;

        $this->lexer->setInput($this->input);

        // Move Lexer to first token
        $this->lexer->moveNext();

        // Parse and return value
        return $this->point();
    }

    /**
     * Match cardinal direction and return sign.
     *
     * @throws RangeException
     */
    private function cardinal(int|float $value): int|float
    {
        // If the cardinal direction was not on the previous coordinate, it can be anything
        if (null === $this->nextCardinal) {
            $this->nextCardinal = Lexer::T_CARDINAL_LON === $this->lexer->lookahead?->type ? Lexer::T_CARDINAL_LON : Lexer::T_CARDINAL_LAT;
        }

        // Match the cardinal direction
        /** @var string $cardinal N, W, S, E, or n, w, s, e */
        $cardinal = $this->match($this->nextCardinal);
        // By default, don't change sign
        $sign = 1;
        // Define value range
        $range = 0;

        switch (strtolower($cardinal)) {
            case 's':
                // Southern latitudes are negative
                $sign = -1;
                // no break
            case 'n':
                // Set requirement for second coordinate
                $this->nextCardinal = Lexer::T_CARDINAL_LON;
                // Latitude values are +/- 90
                $range = 90;
                break;
            case 'w':
                // Western longitudes are negative
                $sign = -1;
                // no break
            case 'e':
                // Set requirement for second coordinate
                $this->nextCardinal = Lexer::T_CARDINAL_LAT;
                // Longitude values are +/- 180
                $range = 180;
                break;
        }

        // Throw exception if value is out of range
        if ($value > $range) {
            throw $this->rangeError('Degrees', $range, -1 * $range);
        }

        // Return value with sign
        return $value * $sign;
    }

    /**
     * Match and return single coordinate value.
     */
    private function coordinate(): float|int
    {
        // By default, don't change sign
        $sign = false;

        // Match sign if the cardinal direction has not been seen
        if (!($this->nextCardinal > 0) && $this->lexer->isNextTokenAny([Lexer::T_PLUS, Lexer::T_MINUS])) {
            $sign = $this->sign();
        }

        // Get coordinate value
        $coordinate = $this->degrees();

        // If sign not matched determine sign from cardinal direction when required
        // or if a cardinal direction is present and this is first coordinate in a pair
        if (false === $sign && ($this->nextCardinal > 0 || (null === $this->nextCardinal && $this->lexer->isNextTokenAny([Lexer::T_CARDINAL_LAT, Lexer::T_CARDINAL_LON])))) {
            return $this->cardinal($coordinate);
        }

        // Remember there was no cardinal direction on first coordinate
        $this->nextCardinal = -1;

        // Return value with sign if it's set
        return (false === $sign ? 1 : $sign) * $coordinate;
    }

    /**
     * Match and return degree value.
     */
    private function degrees(): float|int
    {
        // Reset symbol requirement
        if (Lexer::T_APOSTROPHE === $this->nextSymbol || Lexer::T_QUOTE === $this->nextSymbol) {
            $this->nextSymbol = Lexer::T_DEGREE;
        }

        // If degrees is a float, there will be no minutes or seconds
        if ($this->lexer->isNextToken(Lexer::T_FLOAT)) {
            // Get degree value
            /** @var float $degrees */
            $degrees = $this->match(Lexer::T_FLOAT);

            // Degree symbol may follow degree float values
            if ($this->lexer->isNextToken(Lexer::T_DEGREE)) {
                $this->match(Lexer::T_DEGREE);

                // Set symbol requirement for next value in pair
                $this->nextSymbol = Lexer::T_DEGREE;
            }

            // Return the float value
            return $degrees;
        }

        // If degrees isn't a float, it must be an integer
        /** @var int $degrees */
        $degrees = $this->number();

        // If a symbol does not follow integer, this value is complete
        if (!$this->symbol()) {
            return $degrees;
        }

        // Grab peek of next token since we can't array dereference result in PHP 5.3
        $glimpse = $this->lexer->glimpse();

        // If a colon hasn't been matched, and next token is a number followed by degree symbol, when tuple separator is space instead of comma, this value is complete
        if (Lexer::T_COLON !== $this->nextSymbol && $this->lexer->isNextTokenAny([Lexer::T_INTEGER, Lexer::T_FLOAT]) && isset($glimpse->type) && Lexer::T_DEGREE === $glimpse->type) {
            return $degrees;
        }

        // Add minutes to value
        $degrees += (float) $this->minutes();

        // Return value
        return $degrees;
    }

    /**
     * Match token and return value.
     */
    private function match(int $token): string|int
    {
        // If the next token isn't type specified throw error
        if (!$this->lexer->isNextToken($token)) {
            throw $this->syntaxError((string) $this->lexer->getLiteral($token));
        }

        // Move lexer to the next token
        $this->lexer->moveNext();

        /** @var Token<int, string|int> $nextToken nextToken cannot be null, because of the above test. */
        $nextToken = $this->lexer->token;

        return $nextToken->value;
    }

    /**
     * Match and return minutes value.
     *
     * @throws RangeException
     */
    private function minutes(): string|int
    {
        // If using colon or minutes is an integer parse value
        if (Lexer::T_COLON === $this->nextSymbol || $this->lexer->isNextToken(Lexer::T_INTEGER)) {
            /** @var int $readMinutes */
            $readMinutes = $this->match(Lexer::T_INTEGER);

            // Throw exception if minutes are greater than 60
            if ($readMinutes > 60) {
                throw $this->rangeError('Minutes', 60);
            }

            // Get fractional minutes
            $minutes = $readMinutes / 60;

            // If using colon and one doesn't follow value is done
            if (Lexer::T_COLON === $this->nextSymbol && !$this->lexer->isNextToken(Lexer::T_COLON)) {
                return (string) $minutes;
            }

            // Match minutes symbol
            $this->symbol();

            // Add seconds to value, then return the result.
            return (string) ((float) $minutes + (float) $this->seconds());
        }

        // If minutes is a float there will be no seconds
        if ($this->lexer->isNextToken(Lexer::T_FLOAT)) {
            $minutes = $this->match(Lexer::T_FLOAT);

            // Throw exception if minutes are greater than 60
            if ($minutes > 60) {
                throw $this->rangeError('Minutes', 60);
            }

            // Get fractional minutes
            $minutes = (string) ((float) $minutes / 60);

            // Match minutes symbol
            $this->symbol();

            // return value
            return $minutes;
        }

        // No minutes were present so return 0
        return 0;
    }

    /**
     * Match integer or float token and return value.
     *
     * @throws UnexpectedValueException
     */
    private function number(): int|string
    {
        // If the next token is a float match, then return it
        if ($this->lexer->isNextToken(Lexer::T_FLOAT)) {
            return $this->match(Lexer::T_FLOAT);
        }

        // If the next token is an integer match, then return it
        if ($this->lexer->isNextToken(Lexer::T_INTEGER)) {
            return $this->match(Lexer::T_INTEGER);
        }

        // Throw exception since no match
        throw $this->syntaxError('LongitudeOne\Geo\String\Lexer::T_INTEGER or LongitudeOne\Geo\String\Lexer::T_FLOAT');
    }

    /**
     * Match and return single value or pair.
     *
     * @return float|int|array<int|float>
     *
     * @throws UnexpectedValueException
     */
    private function point(): float|int|array
    {
        // Get first coordinate value
        $x = $this->coordinate();

        // If no additional tokens return single coordinate
        if (null === $this->lexer->lookahead) {
            return $x;
        }

        // Coordinate pairs may be separated by a comma
        if ($this->lexer->isNextToken(Lexer::T_COMMA)) {
            $this->match(Lexer::T_COMMA);
        }

        // Get second coordinate value
        $y = $this->coordinate();

        // There should be no additional tokens
        if (null !== $this->lexer->lookahead) {
            throw $this->syntaxError('end of string');
        }

        // Return coordinate array
        return [$x, $y];
    }

    /**
     * Create out of range exception.
     */
    private function rangeError(string $type, int $high, ?int $low = null): RangeException
    {
        $range = null === $low ? sprintf('greater than %d', $high) : sprintf('out of range %d to %d', $low, $high);
        $message = sprintf('[Range Error] Error: %s %s in value "%s"', $type, $range, $this->input);

        return new RangeException($message);
    }

    /**
     * Match and return seconds value.
     *
     * @throws RangeException
     */
    private function seconds(): int|string
    {
        // Seconds value can be an integer or float
        if ($this->lexer->isNextTokenAny([Lexer::T_INTEGER, Lexer::T_FLOAT])) {
            $seconds = $this->number();

            // Throw exception if seconds are greater than 60
            if ($seconds > 60) {
                throw $this->rangeError('Seconds', 60);
            }

            // Get fractional seconds
            $seconds = (string) ((float) $seconds / 3600);

            // Match seconds symbol if requirement not colon
            if (Lexer::T_COLON !== $this->nextSymbol) {
                $this->symbol();
            }

            // Return value
            return $seconds;
        }

        // No seconds were present so return 0
        return 0;
    }

    /**
     * Match plus or minus sign and return coefficient.
     *
     * @return int 1 for plus, -1 for minus
     */
    private function sign(): int
    {
        if ($this->lexer->isNextToken(Lexer::T_PLUS)) {
            // Match plus and set sign
            $this->match(Lexer::T_PLUS);

            return 1;
        }

        // Match minus and set sign
        $this->match(Lexer::T_MINUS);

        return -1;
    }

    /**
     * Match value component symbol if required or present.
     */
    private function symbol(): bool|int|null
    {
        // If the symbol requirement is not set and the next token is a colon, then match this colon
        if (null === $this->nextSymbol && $this->lexer->isNextToken(Lexer::T_COLON)) {
            $this->match(Lexer::T_COLON);

            // Set symbol requirement for any remaining value
            return $this->nextSymbol = Lexer::T_COLON;
        }

        // If the symbol requirement is not set and the next token is a degree symbol, then match this degree symbol
        if (null === $this->nextSymbol && $this->lexer->isNextToken(Lexer::T_DEGREE)) {
            $this->match(Lexer::T_DEGREE);

            // Set requirement for any remaining value
            return $this->nextSymbol = Lexer::T_APOSTROPHE;
        }

        // Match symbol if requirement set and update requirement for next symbol
        switch ($this->nextSymbol) {
            case Lexer::T_COLON:
                $this->match(Lexer::T_COLON);

                return $this->nextSymbol;
            case Lexer::T_DEGREE:
                $this->match(Lexer::T_DEGREE);

                // The next symbol will be minutes
                return $this->nextSymbol = Lexer::T_APOSTROPHE;
            case Lexer::T_APOSTROPHE:
                $this->match(Lexer::T_APOSTROPHE);

                // The next symbol will be seconds
                return $this->nextSymbol = Lexer::T_QUOTE;
            case Lexer::T_QUOTE:
                $this->match(Lexer::T_QUOTE);

                return $this->nextSymbol;
        }

        // Set requirement for any remaining value
        return $this->nextSymbol = false;
    }

    /**
     * Create exception with a descriptive error message.
     */
    private function syntaxError(string $expected): UnexpectedValueException
    {
        $expected = sprintf('Expected %s, got', $expected);
        $token = $this->lexer->lookahead;
        $found = null === $token ? 'end of string.' : sprintf('"%s"', $token->value);

        $message = sprintf(
            '[Syntax Error] line 0, col %d: Error: %s %s in value "%s"',
            $token->position ?? -1,
            $expected,
            $found,
            $this->input
        );

        return new UnexpectedValueException($message);
    }
}
