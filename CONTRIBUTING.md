# Contributing

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

## Quality tools

> [!NOTE]
> We assume that PHP, PCOV, and Composer are installed locally.

### Installation

The following command updates only the four isolated quality-tool environments. Run it intentionally and review the resulting lock-file changes:

```bash
composer update-tools
```

### Run the quality checks

Run the quality checks from the project root before committing.

```bash
composer quality
```

The following command applies PHP-CS-Fixer changes:

```bash
composer cs-fix
```

### Run the tests

The following command runs the tests without coverage. Use `composer coverage` and `composer coverage-check` to enforce 99% coverage.

```bash
composer test
```

## Composer commands

Prepare the PHP and Markdown tools before running quality checks:

```bash
composer install
composer update-tools
npm install --ignore-scripts
```

`composer update-tools` updates only the four isolated PHP quality environments.
Use `composer update` separately to update the library dependencies. Composer
lockfiles remain local and ignored.

| Command | Purpose |
| --- | --- |
| `composer test` | Run PHPUnit without coverage |
| `composer coverage` | Generate coverage reports |
| `composer coverage-check` | Check the coverage threshold |
| `composer cs-check` | Check formatting without changing files |
| `composer cs-fix` | Apply formatting corrections |
| `composer phpstan` | Run static analysis |
| `composer phpmd` | Analyze production code and tests |
| `composer phpcs` | Check coding standards |
| `composer markdown` | Check Markdown |
| `composer quality` | Check PHP and Markdown without changing source files |
| `composer update-tools` | Update PHP quality tools |
| `composer audit-dependencies` | Audit PHP dependencies |
| `composer check` | Run all checks |

The coverage threshold is 99%.
