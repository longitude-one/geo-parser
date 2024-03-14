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

namespace LongitudeOne\Geo\String\Tests;

use Doctrine\Common\Lexer\Token;
use Generator;
use LongitudeOne\Geo\String\Lexer;
use PHPUnit\Framework\TestCase;

/**
 * Lexer tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class LexerTest extends TestCase
{
    /**
     * return Generator<array{string, Token<int, int|string>[]}>.
     *
     * @return \Generator<array{string, Token[]}>
     */
    public static function dataProvider(): \Generator
    {
        yield ['15', [new Token(15, Lexer::T_INTEGER, 0)]];
        yield ['1E5', [new Token(100000, Lexer::T_FLOAT, 0)]]; // 1E5 is 100000
        yield ['1e5', [new Token(100000, Lexer::T_FLOAT, 0)]];
        yield ['1.5E5', [new Token(150000, Lexer::T_FLOAT, 0)]]; // 1.5E5 is 150000
        yield ['1e-5', [new Token('0.00001', Lexer::T_FLOAT, 0)]]; // 1e-5 is 0.00001
        yield ['0.00001', [new Token('0.00001', Lexer::T_FLOAT, 0)]]; // 1e-5 is 0.00001
        yield ['40° 26\' 46" N', [
            new Token(40, Lexer::T_INTEGER, 0),
            new Token('°', Lexer::T_DEGREE, 2),
            new Token(26, Lexer::T_INTEGER, 5),
            new Token('\'', Lexer::T_APOSTROPHE, 7),
            new Token(46, Lexer::T_INTEGER, 9),
            new Token('"', Lexer::T_QUOTE, 11),
            new Token('N', Lexer::T_CARDINAL_LAT, 13),
        ]];
        yield ['40° 26\' 46" N 79° 58\' 56" W', [
            new Token(40, Lexer::T_INTEGER, 0),
            new Token('°', Lexer::T_DEGREE, 2),
            new Token(26, Lexer::T_INTEGER, 5),
            new Token('\'', Lexer::T_APOSTROPHE, 7),
            new Token(46, Lexer::T_INTEGER, 9),
            new Token('"', Lexer::T_QUOTE, 11),
            new Token('N', Lexer::T_CARDINAL_LAT, 13),
            new Token(79, Lexer::T_INTEGER, 15),
            new Token('°', Lexer::T_DEGREE, 17),
            new Token(58, Lexer::T_INTEGER, 20),
            new Token('\'', Lexer::T_APOSTROPHE, 22),
            new Token(56, Lexer::T_INTEGER, 24),
            new Token('"', Lexer::T_QUOTE, 26),
            new Token('W', Lexer::T_CARDINAL_LON, 28),
        ]];
        yield ['40°26\'46"N 79°58\'56"W', [
            new Token(40, Lexer::T_INTEGER, 0),
            new Token('°', Lexer::T_DEGREE, 2),
            new Token(26, Lexer::T_INTEGER, 4),
            new Token('\'', Lexer::T_APOSTROPHE, 6),
            new Token(46, Lexer::T_INTEGER, 7),
            new Token('"', Lexer::T_QUOTE, 9),
            new Token('N', Lexer::T_CARDINAL_LAT, 10),
            new Token(79, Lexer::T_INTEGER, 12),
            new Token('°', Lexer::T_DEGREE, 14),
            new Token(58, Lexer::T_INTEGER, 16),
            new Token('\'', Lexer::T_APOSTROPHE, 18),
            new Token(56, Lexer::T_INTEGER, 19),
            new Token('"', Lexer::T_QUOTE, 21),
            new Token('W', Lexer::T_CARDINAL_LON, 22),
        ]];
        yield ['40°26\'46"N, 79°58\'56"W', [
            new Token(40, Lexer::T_INTEGER, 0),
            new Token('°', Lexer::T_DEGREE, 2),
            new Token(26, Lexer::T_INTEGER, 4),
            new Token('\'', Lexer::T_APOSTROPHE, 6),
            new Token(46, Lexer::T_INTEGER, 7),
            new Token('"', Lexer::T_QUOTE, 9),
            new Token('N', Lexer::T_CARDINAL_LAT, 10),
            new Token(',', Lexer::T_COMMA, 11),
            new Token(79, Lexer::T_INTEGER, 13),
            new Token('°', Lexer::T_DEGREE, 15),
            new Token(58, Lexer::T_INTEGER, 17),
            new Token('\'', Lexer::T_APOSTROPHE, 19),
            new Token(56, Lexer::T_INTEGER, 20),
            new Token('"', Lexer::T_QUOTE, 22),
            new Token('W', Lexer::T_CARDINAL_LON, 23),
        ]];
        yield ['40.4738° N, 79.553° W', [
            new Token('40.4738', Lexer::T_FLOAT, 0),
            new Token('°', Lexer::T_DEGREE, 7),
            new Token('N', Lexer::T_CARDINAL_LAT, 10),
            new Token(',', Lexer::T_COMMA, 11),
            new Token('79.553', Lexer::T_FLOAT, 13),
            new Token('°', Lexer::T_DEGREE, 19),
            new Token('W', Lexer::T_CARDINAL_LON, 22),
        ]];
        yield ['40.4738°, 79.553°', [
            new Token('40.4738', Lexer::T_FLOAT, 0),
            new Token('°', Lexer::T_DEGREE, 7),
            new Token(',', Lexer::T_COMMA, 9),
            new Token('79.553', Lexer::T_FLOAT, 11),
            new Token('°', Lexer::T_DEGREE, 17),
        ]];
        yield ['40.4738° -79.553°', [
            new Token('40.4738', Lexer::T_FLOAT, 0),
            new Token('°', Lexer::T_DEGREE, 7),
            new Token('-', Lexer::T_MINUS, 10),
            new Token('79.553', Lexer::T_FLOAT, 11),
            new Token('°', Lexer::T_DEGREE, 17),
        ]];
        yield ["40.4738° \t -79.553°", [
            new Token('40.4738', Lexer::T_FLOAT, 0),
            new Token('°', Lexer::T_DEGREE, 7),
            new Token('-', Lexer::T_MINUS, 12),
            new Token('79.553', Lexer::T_FLOAT, 13),
            new Token('°', Lexer::T_DEGREE, 19),
        ]];
    }

    /**
     * @dataProvider dataProvider
     *
     * @param Token<int, string|int>[] $expectedTokens
     */
    public function testLexer(string $input, array $expectedTokens): void
    {
        $lexer = new Lexer($input);
        $index = 0;

        while (null !== $actual = $lexer->peek()) {
            self::assertEquals($expectedTokens[$index++], $actual);
        }
    }

    public function testReusedLexer(): void
    {
        $lexer = new Lexer();

        foreach (self::dataProvider() as $data) {
            $input = $data[0];
            $expectedTokens = $data[1];
            $index = 0;

            $lexer->setInput($input);

            while (null !== $actual = $lexer->peek()) {
                self::assertEquals($expectedTokens[$index++], $actual);
            }
        }
    }
}
