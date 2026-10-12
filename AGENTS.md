# Repository Guidelines

## Project Structure & Module Organization

This library parses geographic coordinate strings and requires PHP 8.3 or later; CI tests PHP 8.3, 8.4, and 8.5. Code uses PSR-4: `LongitudeOne\GeoParser\` maps to `lib/`. Public classes include `Lexer`, `Parser`, `Coordinate`, and `Point`. The axis enum is `LongitudeOne\Core\Enum\AxisEnum`, provided by `longitude-one/spatial-core`. Keep parser implementation details in `lib/Internal/` and library exceptions in `lib/Exception/`.

`parse()` returns a scalar or numeric pair. `parseAsCoordinates()` returns a `Coordinate` or `Point`; an axis is present only when the input explicitly contains `N`, `S`, `E`, or `W`.

Tests use the `LongitudeOne\GeoParser\Tests\` namespace, mapped to `tests/`. Quality-tool configuration lives in `quality/`; `phpunit.xml.dist` defines the test suite and coverage output under `.phpunit.cache/`. The repository no longer provides a Docker environment.

## Build, Test, and Development Commands

- `composer test` runs PHPUnit, enforces 99% line coverage, and prints the summary. It requires PHP, Composer, and PCOV/Xdebug coverage support.
- `composer quality` runs PHP-CS-Fixer in check mode, PHPStan at level 9, and PHPMD against code and tests.
- `composer fix` applies the configured PHP-CS-Fixer rules.
- `composer update-quality-tools` updates project and quality-tool dependencies; review lockfile changes carefully.

Install project dependencies with `composer install`. Quality tools have separate Composer projects in `quality/php-cs-fixer/`, `quality/php-stan/`, and `quality/php-mess-detector/`; run `composer install --working-dir=<directory>` for each before running quality checks. PHPMD requires `^3.0.0` and uses YAML rulesets through the `analyze` command; dependency migrations must be assessed explicitly.

## Coding Style & Naming Conventions

Follow the PHP-CS-Fixer configuration exactly: Symfony-style formatting, `declare(strict_types=1);`, and the standard project copyright header in every PHP file. Use four spaces, typed properties and return types, and PSR-4 namespaces beginning with `LongitudeOne\GeoParser`.

Name classes in `PascalCase`, methods in `camelCase`, and test files as `<Subject>Test.php`. Keep public API changes limited to documented supported classes; other implementation classes are internal and may change.

## Testing Guidelines

Write PHPUnit tests with `PHPUnit\Framework\TestCase`; use data providers for coordinate-format matrices and boundary cases. Test `parse()` and `parseAsCoordinates()` when changing parsing behavior; do not infer an axis where no cardinal is present. Add a regression test for every bug fix, including invalid syntax and geographic range failures when applicable. Run `composer test` and `composer quality` before submitting changes.

## Commit & Pull Request Guidelines

Use Conventional Commits: `type(scope): description`, for example `fix(parser): reject invalid cardinal pair`. Common types are `feat`, `fix`, `refactor`, `docs`, `quality`, `test`, `ci`, and `chore`. Keep commits focused.

Pull requests should explain what changed and why, link the relevant issue when available, and include tests that demonstrate fixes or new behavior. Update documentation when public behavior or supported formats change.

`main` and `dev` are protected: do not push directly to them. Publish changes on a working branch and open a pull request. Stay on the current working branch unless the task requires switching branches.

The workflow in `.github/workflows/ci.yml` runs on `pull_request` and every Friday at 17:00 UTC (`0 17 * * 5`); it has no push trigger. Pull requests limited to Markdown files or `LICENSE` do not trigger CI. The stable PHP 8.3 job runs coverage and quality checks; the remaining jobs test newer PHP versions and development versions of Doctrine Lexer after the stable job succeeds.
