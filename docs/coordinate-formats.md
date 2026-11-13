# Geographic coordinate formats

Supported inputs, return values and parsing exceptions.
See [the README](../README.md) for installation and usage.

## Supported formats

Both single values and coordinate pairs are supported. The following examples
illustrate the supported formats; they
are not exhaustive.

A numeric sign may accompany a cardinal direction when both agree: `+40N` ,
`+75E` , `-40S` , and `-75W`
are accepted. The direction is applied once (`-75W` returns `-75` ); conflicting
combinations such as `-40N`
or `+75W` throw `UnexpectedValueException` . This also applies to DDM/DMS
formats and coordinate pairs,
for both parsing methods. Previously rejected signed cardinal inputs are now
accepted when consistent (issue #31).

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

4. Single values of signed integer degrees with degree symbol, and decimal
   minutes with apostrophe

   - 40° 26.222'
   - -65° 32.22'
   - +165° 52.22'

5. Single values of unsigned integer degrees with degree symbol, decimal
   minutes with apostrophe, and cardinal direction

   - 40° 26.222' E
   - 65° 32.22' S

6. Single values of signed integer degrees with degree symbol, integer minutes
   with apostrophe, and optional integer or decimal seconds with quote

   - 40° 26' 46"
   - -79° 58' 56"
   - 93° 19' 25.8"
   - +120° 19' 25.8"

7. Single values of signed integer degrees with colon symbol, integer minutes,
   and optional colon and integer or decimal seconds

   - +40:26:46
   - -79:58:56
   - 93:19:25.8

8. Single values of unsigned integer degrees with degree symbol, integer
   minutes with apostrophe, optional integer or decimal seconds with quote, and
   cardinal direction

   - 40° 26' 46" S
   - 99° 58' 56" W
   - 44° 58' 53.9" N

9. Single values of unsigned integer degrees with a colon, integer minutes,
   optional integer or decimal seconds, and a cardinal direction

   - 40:26:46 S
   - 99:58:56 W
   - 44:58:53.9 N

10. Two of any one format separated by whitespace

11. Two of any one format separated by a comma

## Return

The parser returns an integer or float for a single value, or an array
containing a coordinate pair.

String inputs are tokenized and parsed as geographic coordinates. Native integer
and float inputs are returned unchanged,
including their type, without passing through the lexer or geographic range
validation.

## Exceptions

Untrusted values in exception messages are formatted by spatial-core's
`DiagnosticValueFormatter` :
control characters and invalid UTF-8 bytes are escaped, and each value is
limited to 2,048 characters.

Library exceptions thrown by the `Lexer` and `Parser` implement the
`LongitudeOne\GeoParser\Exception\ExceptionInterface` interface:

```php
use LongitudeOne\GeoParser\Exception\ExceptionInterface;
use LongitudeOne\GeoParser\Parser;

try {
    $value = (new Parser('100N'))->parse();
} catch (ExceptionInterface $exception) {
    // Handle a library parsing exception.
}
```
