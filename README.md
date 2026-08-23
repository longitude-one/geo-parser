# Geographic parser: longitude-one/geo-parser

![longitude-one/geo-parser](https://img.shields.io/badge/longitude--one-geo--parser-blue)
![Stable release](https://img.shields.io/github/v/release/longitude-one/geo-parser)
[![Packagist License](https://img.shields.io/packagist/l/longitude-one/geo-parser)](https://github.com/longitude-one/geo-parser/blob/main/LICENSE)

Lexer and parser library for geographic coordinate strings.

[![PHP CI](https://github.com/longitude-one/geo-parser/actions/workflows/ci.yml/badge.svg)](https://github.com/longitude-one/geo-parser/actions/workflows/ci.yml)
[![Coverage Status](https://codecov.io/gh/longitude-one/geo-parser/graph/badge.svg?token=0YC8GNTY8L)](https://codecov.io/gh/longitude-one/geo-parser)
![Minimum PHP Version](https://img.shields.io/packagist/php-v/longitude-one/geo-parser.svg?maxAge=3600)

[![Downloads](https://img.shields.io/packagist/dm/longitude-one/geo-parser.svg)](https://packagist.org/packages/longitude-one/geo-parser)

> [!NOTE]
> This package is the continuation of the now abandoned [creof/geo-parser](https://github.com/creof/geo-parser) package.

## Installation

```bash
composer require longitude-one/geo-parser
```

Current version:

```bash
composer require longitude-one/geo-parser:3.0.4
```

## Usage

The parser supports two usage patterns. Pass the value to parse to the constructor, then call `parse()` on the
resulting `Parser` object:

```php
$input  = '79°56′55″W, 40°26′46″N';

$parser = new Parser($input);

$value = $parser->parse(); // [-79.948611111111, 40.446111111111]
```

When parsing many values, reuse a single `Parser` instance:

```php
$input1 = '56.242 E';
$input2 = '40:26:46 S';

$parser = new Parser();

$value1 = $parser->parse($input1); //56.242
$value2 = $parser->parse($input2); //-40.446111111111
```

## Supported Formats

Both single values and coordinate pairs are supported. The following examples illustrate the supported formats; they
are not exhaustive.

1. Simple single-signed values

   - 40
   - -40
   - -8.543
   - +132
   - +77.2

2. Simple single signed values with degree symbol

   - 40°
   - -40°
   - -5.234°
   - +43°
   - +38.43°

3. Single unsigned values with or without degree symbol, and cardinal direction

   - 40° N
   - 40 S
   - 56.242 E

4. Single values of signed integer degrees with degree symbol, and decimal minutes with apostrophe

   - 40° 26.222'
   - -65° 32.22'
   - +165° 52.22'

5. Single values of unsigned integer degrees with degree symbol, decimal minutes with apostrophe, and cardinal direction

   - 40° 26.222' E
   - 65° 32.22' S

6. Single values of signed integer degrees with degree symbol, integer minutes with apostrophe, and optional integer or decimal seconds with quote

   - 40° 26' 46"
   - -79° 58' 56"
   - 93° 19' 25.8"
   - +120° 19' 25.8"

7. Single values of signed integer degrees with colon symbol, integer minutes, and optional colon and integer or decimal seconds

   - +40:26:46
   - -79:58:56
   - 93:19:25.8

8. Single values of unsigned integer degrees with degree symbol, integer minutes with apostrophe, optional integer or decimal seconds with quote, and cardinal direction

   - 40° 26' 46" S
   - 99° 58' 56" W
   - 44° 58' 53.9" N

9. Single values of unsigned integer degrees with a colon, integer minutes, optional integer or decimal seconds, and a cardinal direction

   - 40:26:46 S
   - 99:58:56 W
   - 44:58:53.9 N

10. Two of any one format separated by whitespace

11. Two of any one format separated by a comma

## Return

The parser returns an integer or float for a single value, or an array containing a coordinate pair.

## Exceptions

The `Lexer` and `Parser` throw exceptions implementing the `LongitudeOne\Geo\String\Exception\ExceptionInterface` interface.

## Roadmap

> [!NOTE]
> A major release may increase the minimum supported PHP version **without introducing any other breaking changes**.

| Version | PHP compatibility           | Tested on       | Doctrine Lexer | Tested with Lexer         | Released     | Active Support   | Security fix     |
|---------|-----------------------------|-----------------|----------------|---------------------------|--------------|------------------|------------------|
| 3       | 8.1 - 8.2 - 8.3 - 8.4 - 8.5 | From 8.1 to 8.5 | ^2.1 - ^3.0    | 2.1 3.0 3.1-xdev 4.0-xdev | 04 May 2024  | 31 August 2026   | 31 December 2026 |
| 4       | 8.3 - 8.4 - 8.5             | 8.3 - 8.4 - 8.5 | ^3.0.1         | 3.0 3.1-xdev 4.0-xdev     | August 2026  | 31 December 2026 | 31 December 2027 |
| 5       | 8.4 - 8.5                   | 8.4 - 8.5       |                | 3.0 3.1-xdev 4.0-xdev     | January 2027 | 31 December 2027 | 31 December 2028 |
| 6       | 8.5                         | 8.5             |                | 3.0 3.1-xdev 4.0-xdev.    | January 2028 | 31 December 2028 | 31 December 2029 |

Version 4 is intended as a transitional release. The only backward compatibility break is the supported PHP versions and the `doctrine/lexer` versions.

PHP versions marked as “tested”, along with every listed Doctrine Lexer version, are part of the continuous integration matrix.

### Support Policy

Only the latest major version receives feature and bug fixes. Non-security issues will not be addressed during the security-fixes period.

Previous major versions may receive security fixes only, according to the roadmap above.
