# Repository Guidelines

## Project Structure & Module Organization

This is a PHP 8.3 library for parsing geographic coordinate strings. Code uses PSR-0 paths under `lib/LongitudeOne/GeoParser/`; public entry points include `Lexer`, `Parser`, and `AxisEnum`. Version 4.0.0 also exposes `Coordinate` and `Point` through `Parser::parseAsCoordinates()`. Keep parser implementation details in `lib/LongitudeOne/GeoParser/Internal/` and library exceptions in `Exception/`.

`parse()` returns a scalar or numeric pair. `parseAsCoordinates()` returns a `Coordinate` or `Point`; an axis is present only when the input explicitly contains `N`, `S`, `E`, or `W`.

Tests mirror the namespace in `tests/LongitudeOne/GeoParser/Tests/`. Quality-tool configuration lives in `quality/`; `phpunit.xml.dist` defines the test suite and coverage output. Docker support is in `docker/` and `compose.yaml`.

## Build, Test, and Development Commands

- `composer test` runs PHPUnit, enforces 99% line coverage, and prints the summary. It requires PHP, Composer, and PCOV/Xdebug coverage support.
- `composer quality` runs PHP-CS-Fixer in check mode, PHPStan at level 9, and PHPMD against code and tests.
- `composer fix` applies the configured PHP-CS-Fixer rules.
- `composer update-quality-tools` updates project and quality-tool dependencies; review lockfile changes carefully.

To use the supported container environment, run `docker compose build`, `docker compose up -d`, then prefix commands with `docker compose exec app`. Use `docker compose exec app composer test-with-docker` for Docker coverage paths.

## Coding Style & Naming Conventions

Follow the PHP-CS-Fixer configuration exactly: Symfony-style formatting, `declare(strict_types=1);`, and the standard project copyright header in every PHP file. Use four spaces, typed properties and return types, and PSR-0 namespaces beginning with `LongitudeOne\GeoParser`.

Name classes in `PascalCase`, methods in `camelCase`, and test files as `<Subject>Test.php`. Keep public API changes limited to documented supported classes; other implementation classes are internal and may change.

## Testing Guidelines

Write PHPUnit tests with `PHPUnit\Framework\TestCase`; use data providers for coordinate-format matrices and boundary cases. Test `parse()` and `parseAsCoordinates()` when changing parsing behavior; do not infer an axis where no cardinal is present. Add a regression test for every bug fix, including invalid syntax and geographic range failures when applicable. Run `composer test` and `composer quality` before submitting changes.

## Commit & Pull Request Guidelines

Use Conventional Commits: `type(scope): description`, for example `fix(parser): reject invalid cardinal pair`. Common types are `feat`, `fix`, `refactor`, `docs`, `quality`, `test`, `ci`, and `chore`. Keep commits focused.

Pull requests should explain what changed and why, link the relevant issue when available, and include tests that demonstrate fixes or new behavior. Update documentation when public behavior or supported formats change.
