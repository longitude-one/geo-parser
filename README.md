# Geo Parser

[![Release](https://img.shields.io/github/v/release/longitude-one/geo-parser?include_prereleases&sort=semver&label=release)](https://github.com/longitude-one/geo-parser/releases)
[![PHP](https://img.shields.io/packagist/php-v/longitude-one/geo-parser?label=php)](https://packagist.org/packages/longitude-one/geo-parser)
[![License](https://img.shields.io/packagist/l/longitude-one/geo-parser)](https://github.com/longitude-one/geo-parser/blob/main/LICENSE)
[![CI](https://github.com/longitude-one/geo-parser/actions/workflows/ci.yml/badge.svg)](https://github.com/longitude-one/geo-parser/actions/workflows/ci.yml)
[![Coverage](https://codecov.io/gh/longitude-one/geo-parser/branch/main/graph/badge.svg)](https://codecov.io/gh/longitude-one/geo-parser/tree/main)
[![Monthly downloads](https://img.shields.io/packagist/dm/longitude-one/geo-parser)](https://packagist.org/packages/longitude-one/geo-parser)
[![Documentation](https://img.shields.io/badge/docs-v4-blue)](https://www.longitude-one.fr/geo-parser/v4/index.html)

Parse geographic coordinate strings into decimal values or typed coordinates.
Use scalar results for simple conversions, or retain explicit cardinal axes with typed results.

## Installation

```bash
composer require longitude-one/geo-parser:^4.1.0
```

## Usage

The parser supports two usage patterns. Pass the value to parse to the
constructor, then call `parse()` on the
resulting `Parser` object:

```php
use LongitudeOne\GeoParser\Parser;

$input  = '79°56′55″W, 40°26′46″N';

$parser = new Parser($input);

$value = $parser->parse(); // [-79.948611111111, 40.446111111111]
```

When parsing many values, reuse a single `Parser` instance:

```php
use LongitudeOne\GeoParser\Parser;

$input1 = '56.242 E';
$input2 = '40:26:46 S';

$parser = new Parser();

$value1 = $parser->parse($input1); //56.242
$value2 = $parser->parse($input2); //-40.446111111111
```

### Typed coordinates

Use `parseAsCoordinates()` when the parsed value should
retain its coordinate structure. It returns a `Coordinate` for a single value
and a `Point` for a pair. An axis is set only when the input contains a cardinal
direction; otherwise `getAxis()` returns `null` .

```php
use LongitudeOne\Core\Enum\AxisEnum;
use LongitudeOne\GeoParser\Coordinate;
use LongitudeOne\GeoParser\Parser;
use LongitudeOne\GeoParser\Point;

$coordinate = (new Parser('40°26\'46"N'))->parseAsCoordinates();

assert($coordinate instanceof Coordinate);
assert(40.44611111111111 === $coordinate->getValue());
assert(AxisEnum::LATITUDE === $coordinate->getAxis());

$point = (new Parser('79°56\'55"W, 40°26\'46"N'))->parseAsCoordinates();

assert($point instanceof Point);
assert(-79.94861111111111 === $point->getFirst()->getValue());
assert(AxisEnum::LONGITUDE === $point->getFirst()->getAxis());
assert(AxisEnum::LATITUDE === $point->getSecond()->getAxis());
```

For example, `(new Parser('40, 79'))->parseAsCoordinates()` returns a `Point`
whose two coordinates both have a `null` axis.

## Documentation

Read the [official documentation](https://www.longitude-one.fr/geo-parser/v4/index.html).

See the [coordinate format guide](docs/coordinate-formats.md) for accepted syntax, return values and exceptions.

## Supported formats

| Format | Example |
| --- | --- |
| Decimal degrees | `-79.948611` |
| Degrees and decimal minutes | `79° 56.91666' W` |
| Degrees, minutes and seconds | `79° 56' 55" W` |
| Colon-separated angles | `40:26:46 N` |
| Coordinate pairs | `79°56′55″W, 40°26′46″N` |

Signs and cardinal directions must agree: `-75W` is accepted; `+75W` is rejected.
Library parsing exceptions implement `LongitudeOne\GeoParser\Exception\ExceptionInterface`.
Native integers and floats are returned unchanged, without geographic range validation.

## Roadmap

> [!NOTE]
> A major release may increase the minimum supported PHP version. Other
> breaking changes are documented in the changelog.

<!-- markdownlint-disable MD013 -->

| Version | PHP compatibility           | Tested on       | Doctrine Lexer | Tested with Lexer         | Released     | Active Support   | Security fix     |
|---------|-----------------------------|-----------------|----------------|---------------------------|--------------|------------------|------------------|
| 3       | 8.1 - 8.2 - 8.3 - 8.4 - 8.5 | From 8.1 to 8.5 | ^2.1 - ^3.0    | 2.1 3.0 3.1-xdev 4.0-xdev | 04 May 2024  | 31 August 2026   | 31 December 2026 |
| 4       | 8.3 - 8.4 - 8.5             | 8.3 - 8.4 - 8.5 | ^3.0.1         | 3.0 3.1-xdev 4.0-xdev     | August 2026  | 31 December 2026 | 31 December 2027 |
| 5       | 8.4 - 8.5                   | 8.4 - 8.5       |                | 3.0 3.1-xdev 4.0-xdev     | January 2027 | 31 December 2027 | 31 December 2028 |
| 6       | 8.5                         | 8.5             |                | 3.0 3.1-xdev 4.0-xdev.    | January 2028 | 31 December 2028 | 31 December 2029 |

<!-- markdownlint-enable MD013 -->

PHP versions marked as “tested”, along with every listed Doctrine Lexer version,
are part of the continuous integration matrix.

### Support Policy

The non-breaking-change guarantee applies only to the `Lexer` and `Parser`
classes. `AxisEnum` is provided by the `longitude-one/spatial-core` dependency.
All other classes are internal implementation details and may change or be
removed in any release.

Only the latest major version receives feature and bug fixes. Non-security
issues will not be addressed during the security-fixes period.

Previous major versions may receive security fixes only, according to the
roadmap above.

## Contributing

See [Contributing](CONTRIBUTING.md) for development setup, checks and contribution guidelines.
