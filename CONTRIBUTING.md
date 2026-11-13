# Contributing

## Before starting

For a bug report, include a minimal reproducer, the expected behavior,
and your PHP and dependency versions.

Discuss significant changes in a GitHub issue before implementing them.
Public API, compatibility, architecture, and unresolved standards decisions
require maintainer approval.

## Development

Work on a dedicated branch and keep each contribution focused.

- Follow the configured coding standards.
- Cover behavior changes with tests and add regression tests for bug fixes.
- Update documentation when usage or behavior changes.
- Preserve supported PHP and dependency versions.
- Do not weaken tests or quality rules to make checks pass.

See [Tests](tests/README.md) and [Quality tools](quality/README.md)
for setup and commands.

## Before committing

```bash
composer test
composer quality
git diff --check
```

Review your changes and stage only files related to the contribution.
Do not commit dependencies, local lockfiles, caches, or generated reports.

Use Conventional Commits, with an optional scope:

```text
fix(parser): handle empty input
docs: clarify installation
```

Use the commit types configured in [.versionrc.json](.versionrc.json).

## Submitting a pull request

Run the complete checks from the repository root:

```bash
composer check
```

Resolve failures before submitting. In the pull request:

- Link the related issue and explain what changed and why.
- Report the checks performed and their results.
- Highlight compatibility changes and any remaining limitations.

Keep checks passing when updating the pull request.
Contributions require successful CI checks and independent review before merge.
