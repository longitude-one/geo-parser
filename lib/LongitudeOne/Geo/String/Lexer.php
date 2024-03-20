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

use Doctrine\Common\Lexer\AbstractLexer;

/**
 * Tokenize geographic coordinates.
 *
 * @extends AbstractLexer<int, int|float|string>
 */
class Lexer extends AbstractLexer
{
    public const T_APOSTROPHE = 12;
    public const T_CARDINAL_LAT = 5;
    public const T_CARDINAL_LON = 6;
    public const T_COLON = 11;
    public const T_COMMA = 7;
    public const T_DEGREE = 14;
    public const T_FLOAT = 4;
    public const T_INTEGER = 2;
    public const T_MINUS = 9;
    public const T_NONE = 1;
    public const T_PERIOD = 10;
    public const T_PLUS = 8;
    public const T_QUOTE = 13;

    /**
     * @param string|null $input
     */
    public function __construct($input = null)
    {
        if (!is_null($input) && !is_string($input)) {
            trigger_error(sprintf(
                'Passing a non-string "%s" value in LongitudeOne\Geo\String\Lexer::__construct() is deprecated',
                $input), E_USER_DEPRECATED);
        }

        if (null !== $input) {
            $this->setInput((string) $input);
        }
    }

    /**
     * @return string[]
     */
    protected function getCatchablePatterns(): array
    {
        return [
            '[nesw\'",]',
            // By combining these parts, this regular expression can identify decimal numbers in various formats,
            // including with or without a decimal part and/or scientific notation.
            '(?:[0-9]+)(?:[\.][0-9]+)?(?:e[+-]?[0-9]+)?',
        ];
    }

    /**
     * @return string[]
     */
    protected function getNonCatchablePatterns(): array
    {
        return ['\s+'];
    }

    /**
     * Retrieve a token type.
     * Also processes the token value if necessary.
     *
     * @param string &$value
     */
    protected function getType(&$value): int
    {
        if (is_numeric($value)) {
            $value += 0;

            if (is_int($value)) {
                return self::T_INTEGER;
            }
            $value = (string) $value;

            return self::T_FLOAT;
        }

        return match ($value) {
            ':' => self::T_COLON,
            '\'', "\xe2\x80\xb2" => self::T_APOSTROPHE,
            '"', "\xe2\x80\xb3" => self::T_QUOTE,
            ',' => self::T_COMMA,
            '-' => self::T_MINUS,
            '+' => self::T_PLUS,
            '°' => self::T_DEGREE,
            'N', 'S' => self::T_CARDINAL_LAT,
            'E', 'W' => self::T_CARDINAL_LON,
            default => self::T_NONE,
        };
    }
}
