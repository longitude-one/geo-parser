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
> We assume that PHP, PCOV, and Composer are installed locally. If you do not want to install them, refer to the Docker section.

### Installation

The following command updates Composer dependencies for the project and all quality tools. Run it intentionally and review the resulting lock-file changes:

```bash
composer update-quality-tools
```

### Run the quality checks

Run the quality checks from the project root before committing.

```bash
composer quality
```

The following command applies PHP-CS-Fixer changes:

```bash
composer fix
```

### Run the tests

The following command runs the tests and shows the code-coverage status.

```bash
composer test
```

## Docker (optional)

Docker is an optional aid that provides the supported PHP 8.3 environment and PCOV. Install [Docker Desktop](https://www.docker.com/products/docker-desktop), then build and start the container:

```bash
docker compose build
docker compose up -d
```

Prefix any Composer command above with `docker compose exec app`. For example:

```bash
docker compose exec app composer quality
docker compose exec app composer fix
docker compose exec app composer test-with-docker
```

When running tests in Docker, use `test-with-docker` to generate a local code-coverage report with paths rewritten from `/var/www`:

```bash
docker compose exec app composer test-with-docker
```
