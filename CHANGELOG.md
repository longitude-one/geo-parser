# Changelog

All notable changes to this project will be documented in this file. See [commit-and-tag-version](https://github.com/absolute-version/commit-and-tag-version) for commit guidelines.

## [4.0.0-RC.1](https://github.com/longitude-one/geo-parser/compare/3.0.3...4.0.0-RC.1) (2026-08-08)

### ⚠ BREAKING CHANGES

* PHP8.1 and 8.2 no more supported
* **Parser:** Native integer and float inputs are now returned unchanged instead of being normalized by the lexer. This preserves their values and types but may affect callers that rely on implicit conversion or parser validation.

### ✨ New Features

* Add strict types declaration to all files ([7be229c](https://github.com/longitude-one/geo-parser/commit/7be229c359041d3f8c10956341859d338714daa2))
* Add unit tests for Angle, Cardinal, Parser, and TokenStream classes ([9493d6a](https://github.com/longitude-one/geo-parser/commit/9493d6a85777469583b43dfd58abcb54211e9c1d))
* Introduce LogicException for handling logic errors in the GeoParser ([2dc0057](https://github.com/longitude-one/geo-parser/commit/2dc005757707a0e2f669c7a99396d157f8455283))
* **Parser:** Return native numeric inputs directly. ([7c7c04f](https://github.com/longitude-one/geo-parser/commit/7c7c04ffb5f728772378b1e5a0155afb67f96a50))
* Refactor Parser class to streamline input handling and parsing logic ([947a5b1](https://github.com/longitude-one/geo-parser/commit/947a5b154780c305ccce99a92c290e4df03a395e))

### 🐛 Bug Fixes

* add input validation in Parser constructor and corresponding test ([8e7b08d](https://github.com/longitude-one/geo-parser/commit/8e7b08d8623d2ef79e0392e87a6700464121ea4a))

### ♻️ Refactoring

* enhance symbol matching logic and coordinate handling in Parser class ([97a9d2a](https://github.com/longitude-one/geo-parser/commit/97a9d2a7504aa5e43c1a604abb662421a8413a12))
* improve handling of space-separated coordinate pairs in Parser class ([854d1a3](https://github.com/longitude-one/geo-parser/commit/854d1a39a1ad128ba8c98242cb05c20d7122617e))
* simplify symbol matching logic in Parser class ([e462dbf](https://github.com/longitude-one/geo-parser/commit/e462dbf2ce15d46875962bb933bea34be50c10a2))
* streamline cardinal direction handling in Parser class ([6bf081f](https://github.com/longitude-one/geo-parser/commit/6bf081fc9ffcbb44b4f2f00fda86606bab0c19f2))
* update minutes and seconds methods to return float values and normalize fractions ([e324539](https://github.com/longitude-one/geo-parser/commit/e324539e24bdee12a708923a3d83857abc10d137))

### 📚 Documentation

* Update contributing guidelines and enhance quality tool commands ([2669781](https://github.com/longitude-one/geo-parser/commit/26697813064f30ccd48ccd19f5fc000771a85a5a))

### 🌳 Environmental Impact

* **ci:** reduce cache storage ([cc0beeb](https://github.com/longitude-one/geo-parser/commit/cc0beeb0ead79dc12e7c6f63ea6febd2ebdde4a9))

### 👷 CI/CD

* correct composer cache directory command in CI workflow ([cf8e898](https://github.com/longitude-one/geo-parser/commit/cf8e8983b31a34d837fb8d42523c1f1972a27781))
* update CI workflow to conditionally run full CI based on open pull requests ([689d8ff](https://github.com/longitude-one/geo-parser/commit/689d8ff7a35ec6dd4a2306ca5f7ceedfa6b6187a))
* update composer require command to use caret versioning for lexer dependency ([926c401](https://github.com/longitude-one/geo-parser/commit/926c401c3c08b8c747de1f09f8d03f8c60cbf533))
* update output setting for Composer cache directory in CI workflow ([ff61117](https://github.com/longitude-one/geo-parser/commit/ff611174c96bff738cc1c0c440a3a0639e00f26e))

### 🔧 Maintenance

* add a script to use php-xs-fixer ([c7414cb](https://github.com/longitude-one/geo-parser/commit/c7414cbf0490532bca87be5b791897c427045d61))
* **release:** v4.0.0-RC.0 🎉 ([53d12d1](https://github.com/longitude-one/geo-parser/commit/53d12d1cdca4a7692468ae3cf611de153529fdbe))
* update headers ([67c68c4](https://github.com/longitude-one/geo-parser/commit/67c68c46230cca0cff5d73423a12fd3faf00f19f))
* Update PHP version requirement to ^8.3 and adjust doctrine/lexer dependency ([86be558](https://github.com/longitude-one/geo-parser/commit/86be558dfe6de2ac2eecc3f112562101aaeaaeec))

### 📊​ Quality tools

* Clean up PHPStan baseline by removing outdated ignore errors ([d80aa7b](https://github.com/longitude-one/geo-parser/commit/d80aa7ba312831f8949ad1e5fd397fc1c77a6472))
* exclude TooManyPublicMethods rule in code size checks for tests ([8b03143](https://github.com/longitude-one/geo-parser/commit/8b031433ed6b566618127f897a92e7f0163f162d))
* Update PHP CS Fixer rules to include case statements in ordered class elements ([c4544d7](https://github.com/longitude-one/geo-parser/commit/c4544d77f0824e736655411789ed30beca6f7329))
* update project metadata in php-cs-fixer configuration file ([190e316](https://github.com/longitude-one/geo-parser/commit/190e3167b0e17c49af9d33fe73ef3bfcb1e40b3b))

### 📗​ PHPUnit tests

* add regression tests for fractional coordinate components in FloatTest ([499eba9](https://github.com/longitude-one/geo-parser/commit/499eba91f978243d787c6ab060f7f0d7d556da43))
* PHPUnit version upgraded ([ffa9cf1](https://github.com/longitude-one/geo-parser/commit/ffa9cf185002548404d27e833bbe6fc7fdaa8d64))

## [4.0.0-RC.0](https://github.com/longitude-one/geo-parser/compare/3.0.3...4.0.0-RC.0) (2026-08-07)

### ⚠ BREAKING CHANGES

* PHP8.1 and 8.2 no more supported

### 🐛 Bug Fixes

* add input validation in Parser constructor and corresponding test ([8e7b08d](https://github.com/longitude-one/geo-parser/commit/8e7b08d8623d2ef79e0392e87a6700464121ea4a))

### ♻️ Refactoring

* enhance symbol matching logic and coordinate handling in Parser class ([97a9d2a](https://github.com/longitude-one/geo-parser/commit/97a9d2a7504aa5e43c1a604abb662421a8413a12))
* improve handling of space-separated coordinate pairs in Parser class ([854d1a3](https://github.com/longitude-one/geo-parser/commit/854d1a39a1ad128ba8c98242cb05c20d7122617e))
* simplify symbol matching logic in Parser class ([e462dbf](https://github.com/longitude-one/geo-parser/commit/e462dbf2ce15d46875962bb933bea34be50c10a2))
* streamline cardinal direction handling in Parser class ([6bf081f](https://github.com/longitude-one/geo-parser/commit/6bf081fc9ffcbb44b4f2f00fda86606bab0c19f2))
* update minutes and seconds methods to return float values and normalize fractions ([e324539](https://github.com/longitude-one/geo-parser/commit/e324539e24bdee12a708923a3d83857abc10d137))

### 🌳 Environmental Impact

* **ci:** reduce cache storage ([cc0beeb](https://github.com/longitude-one/geo-parser/commit/cc0beeb0ead79dc12e7c6f63ea6febd2ebdde4a9))

### 👷 CI/CD

* correct composer cache directory command in CI workflow ([cf8e898](https://github.com/longitude-one/geo-parser/commit/cf8e8983b31a34d837fb8d42523c1f1972a27781))
* update CI workflow to conditionally run full CI based on open pull requests ([689d8ff](https://github.com/longitude-one/geo-parser/commit/689d8ff7a35ec6dd4a2306ca5f7ceedfa6b6187a))
* update composer require command to use caret versioning for lexer dependency ([926c401](https://github.com/longitude-one/geo-parser/commit/926c401c3c08b8c747de1f09f8d03f8c60cbf533))
* update output setting for Composer cache directory in CI workflow ([ff61117](https://github.com/longitude-one/geo-parser/commit/ff611174c96bff738cc1c0c440a3a0639e00f26e))

### 🔧 Maintenance

* add a script to use php-xs-fixer ([c7414cb](https://github.com/longitude-one/geo-parser/commit/c7414cbf0490532bca87be5b791897c427045d61))
* update headers ([67c68c4](https://github.com/longitude-one/geo-parser/commit/67c68c46230cca0cff5d73423a12fd3faf00f19f))
* Update PHP version requirement to ^8.3 and adjust doctrine/lexer dependency ([86be558](https://github.com/longitude-one/geo-parser/commit/86be558dfe6de2ac2eecc3f112562101aaeaaeec))

### 📊​ Quality tools

* exclude TooManyPublicMethods rule in code size checks for tests ([8b03143](https://github.com/longitude-one/geo-parser/commit/8b031433ed6b566618127f897a92e7f0163f162d))
* update project metadata in php-cs-fixer configuration file ([190e316](https://github.com/longitude-one/geo-parser/commit/190e3167b0e17c49af9d33fe73ef3bfcb1e40b3b))

### 📗​ PHPUnit tests

* add regression tests for fractional coordinate components in FloatTest ([499eba9](https://github.com/longitude-one/geo-parser/commit/499eba91f978243d787c6ab060f7f0d7d556da43))
* PHPUnit version upgraded ([ffa9cf1](https://github.com/longitude-one/geo-parser/commit/ffa9cf185002548404d27e833bbe6fc7fdaa8d64))

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
