# Contributing

## Docker

To get started, you will need to have Docker installed on your machine.
If you do not have Docker installed, you can [download it](https://www.docker.com/products/docker-desktop).

When you build the Docker container, it will install all the necessary dependencies and container will be ready to handle connection.

```bash
docker compose build
docker compose up
```

Then you can launch the following command to run the tests:

```bash
docker compose exec geo-parser-app vendor/bin/phpunit
```

You could use the embedded composer scripts:

```bash
docker compose exec geo-parser-app composer test
```

This shorcut create a local coverage file `.phpunit.cach/coverage.xml` and display the code coverage report summary :

```bash
docker compose exec geo-parser-app composer test-local
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

## PHP Linters: Php-CS-Fixer, PHP-Mess-detector PHP-Stan

This project uses PHP-CS-Fixer, PHP-Mess-detector and PHP-Stan to ensure code quality.
These tools are set up in the `quality` directory.
Before committing,
you should run some commands to ensure that your code is properly formatted and free of errors.
Read the [readme.md file](./quality/readme.md) for more information.

## Development

- Code formatting MUST follow PSR-2.
- Issues SHOULD include code and/or data to reproduce the issue.
- PR's for issues SHOULD include test(s) for issue.
- PR's SHOULD have adequate documentation (commit messages, comments, etc.) to readily convey what and/or why.

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

- `feat` : New features
- `fix` : Bug fixes
- `perf` : Performance Improvements
- `refactor` : Refactoring
- `docs` : Documentation
- `eco` : Ecological impact
- `ci` : CI/CD
- `chore` : Maintenance
- `quality` : Quality tools
- `test` : PHPUnit tests
