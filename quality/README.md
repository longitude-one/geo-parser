# Using the quality tools

## Getting started

> [!NOTE]
> The first build installs all required dependencies.

```bash
docker compose build
docker compose up -d
```

Run commands in the container with:

```bash
docker compose exec app COMMAND
```

To run all quality tools, use the `quality` Composer script:

```bash
docker compose exec app composer quality
```

## PHP linters: PHP-CS-Fixer, PHPStan, and PHPMD

### PHP-CS-Fixer

To test all files:

```bash
docker compose exec app quality/php-cs-fixer/vendor/bin/php-cs-fixer fix --config=quality/php-cs-fixer/.php-cs-fixer.php --dry-run --allow-risky=yes
```

To fix all files:

```bash
docker compose exec app quality/php-cs-fixer/vendor/bin/php-cs-fixer fix --config=quality/php-cs-fixer/.php-cs-fixer.php --allow-risky=yes
```

### PHPStan

To test files:

```bash
docker compose exec app quality/php-stan/vendor/bin/phpstan analyse --configuration=quality/php-stan/php-stan.neon lib tests --error-format=table --no-progress --no-interaction --no-ansi --level=9 --memory-limit=256M
```

To add current findings to the exception baseline:

```bash
docker compose exec app quality/php-stan/vendor/bin/phpstan analyse --configuration=quality/php-stan/php-stan.neon lib tests --error-format=table --no-progress --no-interaction --no-ansi --level=9 --generate-baseline quality/php-stan/phpstan-baseline.neon
```

### PHPMD

To test files:

```bash
docker compose exec app quality/php-mess-detector/vendor/bin/phpmd lib text quality/php-mess-detector/ruleset.xml
docker compose exec app quality/php-mess-detector/vendor/bin/phpmd tests text quality/php-mess-detector/test-ruleset.xml
```
