# Changelog

All notable changes to this project will be documented in this file. See [commit-and-tag-version](https://github.com/absolute-version/commit-and-tag-version) for commit guidelines.

## [3.0.3](https://github.com/longitude-one/geo-parser/compare/3.0.2...3.0.3) (2026-08-07)

### 📚 Documentation

* Update changelog and improve documentation clarity ([012ed02](https://github.com/longitude-one/geo-parser/commit/012ed02664cd64f4e850e02f2789cb2439a0bc1e))

### 👷 CI/CD

* Add '4-x-dev' branch to CI workflow triggers ([303b3ca](https://github.com/longitude-one/geo-parser/commit/303b3ca79d6c8d70d5beb00eea944643f57300fc))
## [3.0.2](https://github.com/longitude-one/geo-parser/compare/3.0.1...3.0.2) (2026-08-06)

### 🐛 Bug Fixes

* Fix quality errors found by the upgraded PHPStan ([088841e](https://github.com/longitude-one/geo-parser/commit/088841e8c8936745bd882a10d81de31716fd9d82))
* Sanitize exception message values to prevent inflated-message injection ([f97a790](https://github.com/longitude-one/geo-parser/commit/f97a790908e13f0255df8253e36d0b2eef140b84))

### 📚 Documentation

* Report Lexer compatibility and test coverage ([4706459](https://github.com/longitude-one/geo-parser/commit/47064594c8a744ccf042761718faff026c47f4c2))
* Mention the Lexer version ([c28e46c](https://github.com/longitude-one/geo-parser/commit/c28e46c4a8d2d6bdc4f69ce024cd067760bc66ca))
* Add Markdown linting ([94deaea](https://github.com/longitude-one/geo-parser/commit/94deaead9e5fe0dde280937adfdaca5c80ec0ff0))
* Add a roadmap ([6ab4aab](https://github.com/longitude-one/geo-parser/commit/6ab4aaba892240d96f4e04464fbc1c8ee7bc14bc))

### 🌳 Environmental Impact

* Decrease package size ([a2e912b](https://github.com/longitude-one/geo-parser/commit/a2e912b8e566e3ad38b4dac4255ed83494664f09))

### 👷 CI/CD

* Add tests with the next major Lexer version ([c9a8678](https://github.com/longitude-one/geo-parser/commit/c9a86783421d6523d1b403e6bdcf56fc7f20efbb))
* Migrate from Coveralls to Codecov ([1a3923a](https://github.com/longitude-one/geo-parser/commit/1a3923abe4b71aed12b9db0508b17c61e31e7adf))

### 🔧 Maintenance

* Add a missing emoji ([52b899e](https://github.com/longitude-one/geo-parser/commit/52b899e8e13d76db61eb11d748e8014114f7c595))
* Create Composer script shortcuts ([5e25b49](https://github.com/longitude-one/geo-parser/commit/5e25b493510c1d2151c3c17e91f63767f9fa47b7))
* Optimize Dockerfile installation of quality tools ([e5e9ef1](https://github.com/longitude-one/geo-parser/commit/e5e9ef1576a9a5a1f4716b8a1d38062550c7a406))
* Update headers ([e18454a](https://github.com/longitude-one/geo-parser/commit/e18454a9ac8611b96b55710f6671c1714e4ebf3f))

### 📊​ Quality tools

* Add commit-and-tag-version ([e48e43b](https://github.com/longitude-one/geo-parser/commit/e48e43b5f0cf3b793dfb74ed520af0a8594d041c))
* Upgrade PHPStan ([83cf636](https://github.com/longitude-one/geo-parser/commit/83cf6367ff466bac2cfb08164c2b821fc495931c))
* Update the PHPMD ruleset name and maximum permitted method length ([85accad](https://github.com/longitude-one/geo-parser/commit/85accadd750b529dd709d5eacadb7240f1b2c773))

### 📗​ PHPUnit tests

* Add a security test ([95fe6e8](https://github.com/longitude-one/geo-parser/commit/95fe6e827f256c343ffa98cc63323c2737ec51eb))

<!-- markdownlint-disable MD024 -->

## Version 3.0.1

### Fix

- Fix "geo-parser miss some out-of-ranges" [#22](https://github.com/longitude-one/geo-parser/issues/22)

## Version 3.0.0

### Added

- Docker support for development and testing.

### Changed

- Namespaces are now PSR-4 compliant.
- Namespaces are now `LongitudeOne\Geo\Parser` and `LongitudeOne\Geo\Parser\Exception`.
- Updated README.md to reflect new namespaces.

### Removed

- Support for PHP 5.*, 7.1, 7.2, 7.3.

### [2.2.1] - 2019-08-07

#### Changed

- Fix compatibility with doctrine/lexer 1.1.0. PR [#18](https://github.com/creof/geo-parser/pull/18) by [bcremer](https://github.com/bcremer).

### [2.2.0] - 2019-08-06

#### Added

- Support for PHP 7.1 through 7.3

#### Removed

- Support for PHP earlier than 7.1

### [2.1.0] - 2016-05-03

#### Added

- Support for numbers in scientific notation.

#### Changed

- Parser constructor no longer requires a value, enabling instance reuse.
- Lexer constructor no longer requires a value, enabling instance reuse.
- Tests now use Composer autoload.
- PHPUnit config XML now conforms to XSD.
- Documentation updated with new usage pattern.
- Move non-value comparison into if statement in Lexer::getType().
- Test case and test data cleanup.

#### Removed

- The TestInit class is no longer needed.

### [2.0.0] - 2015-11-18

#### Added

- Change base namespace to CrEOF\Geo\String to avoid class collision with other CrEOF packages.

### [1.0.1] - 2015-11-17

#### Added

- Exclude fingerprint for Code Climate fixme engine to ignore "Stub TODO.md file." in changelog.

#### Changed

- Removed code for unused conditions in Parser error methods.
- Removed case for token T_PERIOD in getType method of Lexer, it's not used in Parser.

### [1.0.0] - 2015-11-11

#### Added

- Change log file to chronicle changes.
- Dependency on SPL extension to composer.json since the package exceptions extend them.
- Stub TODO.md file.
- CONTRIBUTING.md file with guidelines.
- Travis CI config
- Code Climate config
- Add support for unicode prime and double prime.
- Tests for uncovered parser branches.

#### Changed

- Use string compare instead of regex for cardinal direction.
- Remove unneeded dependencies for PHPMD and PHPCS; Code Climate handles this.
- Match seconds symbol with symbol().
- Change property names in parser to more accurately indicate what they're for.
