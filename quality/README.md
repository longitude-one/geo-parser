# Quality tools

This directory contains quality tool configurations and the coverage checker.
Run all commands from the repository root.

## Setup

```bash
composer install
composer update-tools
npm install --ignore-scripts
```

## Daily use

```bash
composer quality
```

Checks coding style, static analysis, code quality, and Markdown formatting.

To automatically fix PHP coding style:

```bash
composer cs-fix
```

Review the changes and rerun the checks.

## Before contributing

Before each commit, run:

```bash
composer test
composer quality
```

Before opening or updating a merge request, run:

```bash
composer check
```

This validates Composer configuration, audits dependencies, runs tests with
coverage, checks the coverage threshold, and runs all quality checks.
Coverage requires PCOV.

Resolve all failures before submitting your changes.
