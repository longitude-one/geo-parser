# Contributing

## Docker

To get started, install Docker on your machine. If you do not already have it, you can [download Docker Desktop](https://www.docker.com/products/docker-desktop).

Building and starting the container installs the project dependencies.

```bash
docker compose build
docker compose up -d
```

Run the test suite with:

```bash
docker compose exec app vendor/bin/phpunit
```

You can also use the Composer script:

```bash
docker compose exec app composer test
```

This shortcut creates the local coverage file `.phpunit.cache/coverage.xml` and displays a code-coverage summary:

```bash
docker compose exec app composer test-local
```

```log
PHPUnit 10.5.64 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.1.34 with PCOV 1.0.13-dev
Configuration: /var/www/phpunit.xml.dist

...............................................................  63 / 166 ( 37%)
............................................................... 126 / 166 ( 75%)
........................................                        166 / 166 (100%)

Time: 00:00.151, Memory: 10.00 MB

OK (166 tests, 558 assertions)

Generating code coverage report in Clover XML format ... done [00:00.005]


Code Coverage Report Summary:
  Classes: 100.00% (3/3)
  Methods: 100.00% (19/19)
  Lines:   100.00% (168/168)
```

## PHP linters: PHP-CS-Fixer, PHPMD, and PHPStan

This project uses PHP-CS-Fixer, PHPMD, and PHPStan to ensure code quality.
These tools are set up in the `quality` directory.
Before committing,
you should run some commands to ensure that your code is properly formatted and free of errors.
Read the [quality tools guide](./quality/README.md) for more information.

## Development

- Code formatting MUST follow the project's PHP-CS-Fixer configuration.
- Issues SHOULD include code and/or data to reproduce the issue.
- Pull requests that fix issues SHOULD include tests that reproduce the issue.
- Pull requests SHOULD include adequate documentation (commit messages, comments, and so on) to explain what changed and why.

## Commit message conventions

All commits must follow the [Conventional Commits specification](https://www.conventionalcommits.org/):

```text
type(scope): description
```

Examples:

- `feat(parser): support DMS coordinates`
- `fix(lexer): prevent invalid token parsing`
- `test: add lexer unit tests`

Allowed types include:

- `feat`: New features
- `fix`: Bug fixes
- `perf`: Performance improvements
- `refactor`: Refactoring
- `docs`: Documentation
- `eco`: Ecological impact
- `ci`: CI/CD
- `chore`: Maintenance
- `quality`: Quality tools
- `test`: PHPUnit tests
