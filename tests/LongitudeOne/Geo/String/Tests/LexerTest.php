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

use LongitudeOne\Geo\String\Lexer;

/**
 * Lexer tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class LexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array<int, array<string, array<int, array<string, float|int|string>>|string>>
     */
    public function testDataSource(): array
    {
        return [
            [
                'input' => '15',
                'expectedTokens' => [
                    ['value' => 15, 'type' => Lexer::T_INTEGER, 'position' => 0],
                ],
            ],
            [
                'input' => '1E5',
                'expectedTokens' => [
                    ['value' => 100000, 'type' => Lexer::T_FLOAT, 'position' => 0],
                ],
            ],
            [
                'input' => '1e5',
                'expectedTokens' => [
                    ['value' => 100000, 'type' => Lexer::T_FLOAT, 'position' => 0],
                ],
            ],
            [
                'input' => '1.5E5',
                'expectedTokens' => [
                    ['value' => 150000, 'type' => Lexer::T_FLOAT, 'position' => 0],
                ],
            ],
            [
                'input' => '1E-5',
                'expectedTokens' => [
                    ['value' => 0.00001, 'type' => Lexer::T_FLOAT, 'position' => 0],
                ],
            ],
            [
                'input' => '40° 26\' 46" N',
                'expectedTokens' => [
                    ['value' => 40, 'type' => Lexer::T_INTEGER, 'position' => 0],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 2],
                    ['value' => 26, 'type' => Lexer::T_INTEGER, 'position' => 5],
                    ['value' => '\'', 'type' => Lexer::T_APOSTROPHE, 'position' => 7],
                    ['value' => 46, 'type' => Lexer::T_INTEGER, 'position' => 9],
                    ['value' => '"', 'type' => Lexer::T_QUOTE, 'position' => 11],
                    ['value' => 'N', 'type' => Lexer::T_CARDINAL_LAT, 'position' => 13],
                ],
            ],
            [
                'input' => '40° 26\' 46" N 79° 58\' 56" W',
                'expectedTokens' => [
                    ['value' => 40, 'type' => Lexer::T_INTEGER, 'position' => 0],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 2],
                    ['value' => 26, 'type' => Lexer::T_INTEGER, 'position' => 5],
                    ['value' => '\'', 'type' => Lexer::T_APOSTROPHE, 'position' => 7],
                    ['value' => 46, 'type' => Lexer::T_INTEGER, 'position' => 9],
                    ['value' => '"', 'type' => Lexer::T_QUOTE, 'position' => 11],
                    ['value' => 'N', 'type' => Lexer::T_CARDINAL_LAT, 'position' => 13],
                    ['value' => 79, 'type' => Lexer::T_INTEGER, 'position' => 15],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 17],
                    ['value' => 58, 'type' => Lexer::T_INTEGER, 'position' => 20],
                    ['value' => '\'', 'type' => Lexer::T_APOSTROPHE, 'position' => 22],
                    ['value' => 56, 'type' => Lexer::T_INTEGER, 'position' => 24],
                    ['value' => '"', 'type' => Lexer::T_QUOTE, 'position' => 26],
                    ['value' => 'W', 'type' => Lexer::T_CARDINAL_LON, 'position' => 28],
                ],
            ],
            [
                'input' => '40°26\'46"N 79°58\'56"W',
                'expectedTokens' => [
                    ['value' => 40, 'type' => Lexer::T_INTEGER, 'position' => 0],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 2],
                    ['value' => 26, 'type' => Lexer::T_INTEGER, 'position' => 4],
                    ['value' => '\'', 'type' => Lexer::T_APOSTROPHE, 'position' => 6],
                    ['value' => 46, 'type' => Lexer::T_INTEGER, 'position' => 7],
                    ['value' => '"', 'type' => Lexer::T_QUOTE, 'position' => 9],
                    ['value' => 'N', 'type' => Lexer::T_CARDINAL_LAT, 'position' => 10],
                    ['value' => 79, 'type' => Lexer::T_INTEGER, 'position' => 12],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 14],
                    ['value' => 58, 'type' => Lexer::T_INTEGER, 'position' => 16],
                    ['value' => '\'', 'type' => Lexer::T_APOSTROPHE, 'position' => 18],
                    ['value' => 56, 'type' => Lexer::T_INTEGER, 'position' => 19],
                    ['value' => '"', 'type' => Lexer::T_QUOTE, 'position' => 21],
                    ['value' => 'W', 'type' => Lexer::T_CARDINAL_LON, 'position' => 22],
                ],
            ],
            [
                'input' => '40°26\'46"N, 79°58\'56"W',
                'expectedTokens' => [
                    ['value' => 40, 'type' => Lexer::T_INTEGER, 'position' => 0],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 2],
                    ['value' => 26, 'type' => Lexer::T_INTEGER, 'position' => 4],
                    ['value' => '\'', 'type' => Lexer::T_APOSTROPHE, 'position' => 6],
                    ['value' => 46, 'type' => Lexer::T_INTEGER, 'position' => 7],
                    ['value' => '"', 'type' => Lexer::T_QUOTE, 'position' => 9],
                    ['value' => 'N', 'type' => Lexer::T_CARDINAL_LAT, 'position' => 10],
                    ['value' => ',', 'type' => Lexer::T_COMMA, 'position' => 11],
                    ['value' => 79, 'type' => Lexer::T_INTEGER, 'position' => 13],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 15],
                    ['value' => 58, 'type' => Lexer::T_INTEGER, 'position' => 17],
                    ['value' => '\'', 'type' => Lexer::T_APOSTROPHE, 'position' => 19],
                    ['value' => 56, 'type' => Lexer::T_INTEGER, 'position' => 20],
                    ['value' => '"', 'type' => Lexer::T_QUOTE, 'position' => 22],
                    ['value' => 'W', 'type' => Lexer::T_CARDINAL_LON, 'position' => 23],
                ],
            ],
            [
                'input' => '40.4738° N, 79.553° W',
                'expectedTokens' => [
                    ['value' => 40.4738, 'type' => Lexer::T_FLOAT, 'position' => 0],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 7],
                    ['value' => 'N', 'type' => Lexer::T_CARDINAL_LAT, 'position' => 10],
                    ['value' => ',', 'type' => Lexer::T_COMMA, 'position' => 11],
                    ['value' => 79.553, 'type' => Lexer::T_FLOAT, 'position' => 13],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 19],
                    ['value' => 'W', 'type' => Lexer::T_CARDINAL_LON, 'position' => 22],
                ],
            ],
            [
                'input' => '40.4738°, 79.553°',
                'expectedTokens' => [
                    ['value' => 40.4738, 'type' => Lexer::T_FLOAT, 'position' => 0],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 7],
                    ['value' => ',', 'type' => Lexer::T_COMMA, 'position' => 9],
                    ['value' => 79.553, 'type' => Lexer::T_FLOAT, 'position' => 11],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 17],
                ],
            ],
            [
                'input' => '40.4738° -79.553°',
                'expectedTokens' => [
                    ['value' => 40.4738, 'type' => Lexer::T_FLOAT, 'position' => 0],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 7],
                    ['value' => '-', 'type' => Lexer::T_MINUS, 'position' => 10],
                    ['value' => 79.553, 'type' => Lexer::T_FLOAT, 'position' => 11],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 17],
                ],
            ],
            [
                'input' => "40.4738° \t -79.553°",
                'expectedTokens' => [
                    ['value' => 40.4738, 'type' => Lexer::T_FLOAT, 'position' => 0],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 7],
                    ['value' => '-', 'type' => Lexer::T_MINUS, 'position' => 12],
                    ['value' => 79.553, 'type' => Lexer::T_FLOAT, 'position' => 13],
                    ['value' => '°', 'type' => Lexer::T_DEGREE, 'position' => 19],
                ],
            ],
        ];
    }

    /**
     * @param string $input
     *
     * @dataProvider testDataSource
     */
    public function testLexer($input, array $expectedTokens): void
    {
        $lexer = new Lexer($input);
        $index = 0;

        while (null !== $actual = $lexer->peek()) {
            $this->assertEquals($expectedTokens[$index++], $actual);
        }
    }

    public function testReusedLexer(): void
    {
        $lexer = new Lexer();

        foreach ($this->testDataSource() as $data) {
            $input = $data['input'];
            $expectedTokens = $data['expectedTokens'];
            $index = 0;

            $lexer->setInput($input);

            while (null !== $actual = $lexer->peek()) {
                $this->assertEquals($expectedTokens[$index++], $actual);
            }
        }
    }
}
