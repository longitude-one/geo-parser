# Geographic parser: longitude-one/geo-parser

![longitude-one/geo-parser](https://img.shields.io/badge/longitude--one-geo--parser-blue)
![Stable release](https://img.shields.io/github/v/release/longitude-one/geo-parser)
[![Packagist License](https://img.shields.io/packagist/l/longitude-one/geo-parser)](https://github.com/longitude-one/geo-parser/blob/main/LICENSE)

Lexer and parser library for geographic point string values.

[![PHP CI](https://github.com/longitude-one/geo-parser/actions/workflows/ci.yml/badge.svg)](https://github.com/longitude-one/geo-parser/actions/workflows/ci.yml)
[![Coverage Status](https://codecov.io/gh/longitude-one/geo-parser/graph/badge.svg?token=0YC8GNTY8L)](https://codecov.io/gh/longitude-one/geo-parser)
![Minimum PHP Version](https://img.shields.io/packagist/php-v/longitude-one/geo-parser.svg?maxAge=3600)

[![Downloads](https://img.shields.io/packagist/dm/longitude-one/geo-parser.svg)](https://packagist.org/packages/longitude-one/geo-parser)

> [!NOTE]
> This package is the continuation of the now abandoned [creof/geo-parser](https://github.com/creof/geo-parser) package.

## Installation
## [3.0.2](https://github.com/longitude-one/geo-parser/compare/3.0.1...3.0.2) (2026-08-06)

### 🐛 Bug Fixes

* quality errors found by upgraded PHP-Stan ([088841e](https://github.com/longitude-one/geo-parser/commit/088841e8c8936745bd882a10d81de31716fd9d82))
* sanitize exception message values to prevent inflated messages injection ([f97a790](https://github.com/longitude-one/geo-parser/commit/f97a790908e13f0255df8253e36d0b2eef140b84))

### 📚 Documentation

* Lexer compatibilities and test reported ([4706459](https://github.com/longitude-one/geo-parser/commit/47064594c8a744ccf042761718faff026c47f4c2))
* Lexer version mentioned ([c28e46c](https://github.com/longitude-one/geo-parser/commit/c28e46c4a8d2d6bdc4f69ce024cd067760bc66ca))
* markdown linting ([94deaea](https://github.com/longitude-one/geo-parser/commit/94deaead9e5fe0dde280937adfdaca5c80ec0ff0))
* Roadmap added ([6ab4aab](https://github.com/longitude-one/geo-parser/commit/6ab4aaba892240d96f4e04464fbc1c8ee7bc14bc))

### 🌳 Environmental Impact

* Decrease the package size ([a2e912b](https://github.com/longitude-one/geo-parser/commit/a2e912b8e566e3ad38b4dac4255ed83494664f09))

### 👷 CI/CD

* add test with next lexer major version ([c9a8678](https://github.com/longitude-one/geo-parser/commit/c9a86783421d6523d1b403e6bdcf56fc7f20efbb))
* migration from coveralls to codecov ([1a3923a](https://github.com/longitude-one/geo-parser/commit/1a3923abe4b71aed12b9db0508b17c61e31e7adf))

### 🔧 Maintenance

* add missing emoji ([52b899e](https://github.com/longitude-one/geo-parser/commit/52b899e8e13d76db61eb11d748e8014114f7c595))
* create script-shorcuts for composer ([5e25b49](https://github.com/longitude-one/geo-parser/commit/5e25b493510c1d2151c3c17e91f63767f9fa47b7))
* Dockerfile optimze installations of quality tools ([e5e9ef1](https://github.com/longitude-one/geo-parser/commit/e5e9ef1576a9a5a1f4716b8a1d38062550c7a406))
* headers updated ([e18454a](https://github.com/longitude-one/geo-parser/commit/e18454a9ac8611b96b55710f6671c1714e4ebf3f))

### 📊​ Quality tools

* new tool : commit-and-tag-version ([e48e43b](https://github.com/longitude-one/geo-parser/commit/e48e43b5f0cf3b793dfb74ed520af0a8594d041c))
* PHP-STAN upgraded ([83cf636](https://github.com/longitude-one/geo-parser/commit/83cf6367ff466bac2cfb08164c2b821fc495931c))
* update PHPMD ruleset name and adjust maximum method length allowed ([85accad](https://github.com/longitude-one/geo-parser/commit/85accadd750b529dd709d5eacadb7240f1b2c773))

### 📗​ PHPUnit tests

* Security test added ([95fe6e8](https://github.com/longitude-one/geo-parser/commit/95fe6e827f256c343ffa98cc63323c2737ec51eb))

```bash
composer require longitude-one/geo-parser
```

Current version:

```bash
composer require longitude-one/geo-parser:3.0.2
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

9. Single values of unsigned integer degrees with colon symbol, integer minutes with, optional colon and integer or decimal seconds, and cardinal direction

   - 40:26:46 S
   - 99:58:56 W
   - 44:58:53.9 N

10. Two of any one format separated by whitespace

11. Two of any one format separated by a comma

## Return

The parser will return an integer/float or an array containing a pair of these values.

## Exceptions

The `Lexer` and `Parser` will throw exceptions implementing interface `LongitudeOne\Geo\String\Exception\ExceptionInterface`.

## Roadmap

[!NOTE] A major release may increase the minimum supported PHP version **without introducing any other breaking changes**.

| Version | PHP compatibility           | Tested on       | Doctrine Lexer | Tested with Lexer         | Released     | Active Support   | Security fix     |
|---------|-----------------------------|-----------------|----------------|---------------------------|--------------|------------------|------------------|
| 3       | 8.1 - 8.2 - 8.3 - 8.4 - 8.5 | From 8.1 to 8.5 | ^2.1 - ^3.0    | 2.1 3.0 3.1-xdev 4.0-xdev | 04 May 2024  | 31 August 2026   | 31 December 2026 |
| 4       | 8.3 - 8.4 - 8.5             | 8.3 - 8.4 - 8.5 | ^3.0.1         | 3.0 3.1-xdev 4.0-xdev     | August 2026  | 31 December 2026 | 31 December 2027 |
| 5       | 8.4 - 8.5                   | 8.4 - 8.5       |                | 3.0 3.1-xdev 4.0-xdev     | January 2027 | 31 December 2028 | 31 December 2030 |
| 6       | 8.5                         | 8.5             |                | 3.0 3.1-xdev 4.0-xdev.    | January 2028 | 31 December 2028 | 31 December 2030 |

Version 4 is intended as a transitional release. The only backward compatibility break is the supported PHP versions and the `doctrine/lexer` versions.

PHP versions marked as "tested" and each doctrine lexer version are part of the continuous integration matrix.

### Support Policy

Only the latest major version receives feature and bug fixes. Non-security issues will not be addressed during the security-fixes period.

Previous major versions may receive security fixes only, according to the roadmap above.
