# Tests

This directory contains the automated PHPUnit tests.
Run all commands from the repository root.

## Setup

```bash
composer install
```

## Run tests

Run the complete suite without coverage:

```bash
composer test
```

Run a specific test class or method, replacing `ExampleTest` with its name:

```bash
composer test -- --filter ExampleTest
```

## Coverage

With PCOV installed, generate coverage reports and check the configured threshold:

```bash
composer coverage
composer coverage-check
```

Reports are available in `build/coverage/`, including HTML in
`build/coverage/html/index.html`.

## Contributing

Cover behavior changes and add a regression test for each bug fix.
Run the complete suite before each commit.

See [Quality tools](../quality/README.md) for checks required before submitting
a merge request.
