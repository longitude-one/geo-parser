# Geographic parser: longitude-one/geo-parser

![longitude-one/geo-parser](https://img.shields.io/badge/longitude--one-geo--parser-blue)
![Stable release](https://img.shields.io/github/v/release/longitude-one/geo-parser)
[![Packagist License](https://img.shields.io/packagist/l/longitude-one/geo-parser)](https://github.com/longitude-one/geo-parser/blob/main/LICENSE)

Lexer and parser library for geographic point string values.

[![PHP CI](https://github.com/longitude-one/geo-parser/actions/workflows/ci.yml/badge.svg)](https://github.com/longitude-one/geo-parser/actions/workflows/ci.yml)
[![Maintainability](https://api.codeclimate.com/v1/badges/395f661509f03ebed0ee/maintainability)](https://codeclimate.com/github/longitude-one/geo-parser/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/395f661509f03ebed0ee/test_coverage)](https://codeclimate.com/github/longitude-one/geo-parser/test_coverage)
[![Coverage Status](https://coveralls.io/repos/github/longitude-one/geo-parser/badge.svg)](https://coveralls.io/github/longitude-one/geo-parser)
![Minimum PHP Version](https://img.shields.io/packagist/php-v/longitude-one/geo-parser.svg?maxAge=3600)
[![Tested on PHP 8.1 to 8.3](https://img.shields.io/badge/tested%20on-PHP%20%208.1%20|%208.2%20|%208.3-brightgreen.svg?maxAge=2419200)](https://github.com/longitude-one/geo-parser/actions)

[![Downloads](https://img.shields.io/packagist/dm/longitude-one/geo-parser.svg)](https://packagist.org/packages/longitude-one/geo-parser)

> [!NOTE]
> This package is the continuation of the now abandoned [creof/geo-parser](https://github.com/creof/geo-parser) package.

## Installation

```bash
composer require longitude-one/geo-parser
```

## Usage

There are two use patterns for the parser. The value to be parsed can be passed into the constructor, then parse()
called on the returned ```Parser``` object:

```php
$input  = '79°56′55″W, 40°26′46″N';

$parser = new Parser($input);

$value = $parser->parse(); // [-79.948611111111, 40.446111111111]
```

If many values need to be parsed, a single ```Parser``` instance can be used:

```php
$input1 = '56.242 E';
$input2 = '40:26:46 S';

$parser = new Parser();

$value1 = $parser->parse($input1); //56.242
$value2 = $parser->parse($input2); //-40.446111111111
```

## Supported Formats

Both single values and pairs are supported. Some samples of supported formats are below, though not every possible iteration may be explicitly specified:

1. Simple single-signed values
 * 40
 * -40
 * -8.543
 * +132
 * +77.2

2. Simple single signed values with degree symbol
 * 40°
 * -40°
 * -5.234°
 * +43°
 * +38.43°

3. Single unsigned values with or without degree symbol, and cardinal direction
 * 40° N
 * 40 S
 * 56.242 E

4. Single values of signed integer degrees with degree symbol, and decimal minutes with apostrophe
 * 40° 26.222'
 * -65° 32.22'
 * +165° 52.22'

5. Single values of unsigned integer degrees with degree symbol, decimal minutes with apostrophe, and cardinal direction
 * 40° 26.222' E
 * 65° 32.22' S

6. Single values of signed integer degrees with degree symbol, integer minutes with apostrophe, and optional integer or decimal seconds with quote
 * 40° 26' 46"
 * -79° 58' 56"
 * 93° 19' 25.8"
 * +120° 19' 25.8"

6. Single values of signed integer degrees with colon symbol, integer minutes, and optional colon and integer or decimal seconds
 * +40:26:46
 * -79:58:56
 * 93:19:25.8

7. Single values of unsigned integer degrees with degree symbol, integer minutes with apostrophe, optional integer or decimal seconds with quote, and cardinal direction
 * 40° 26' 46" S
 * 99° 58' 56" W
 * 44° 58' 53.9" N

7. Single values of unsigned integer degrees with colon symbol, integer minutes with, optional colon and integer or decimal seconds, and cardinal direction
 * 40:26:46 S
 * 99:58:56 W
 * 44:58:53.9 N

8. Two of any one format separated by whitespace

9. Two of any one format separated by a comma

## Return

The parser will return an integer/float or an array containing a pair of these values.

## Exceptions

The ```Lexer``` and ```Parser``` will throw exceptions implementing interface ```LongitudeOne\Geo\String\Exception\ExceptionInterface```.
