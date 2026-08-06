#!/bin/sh

# Geo-Parser requirements
if [ ! -d "vendor" ]; then
    composer install
fi

# PHP-CS-FIXER requirements
if [ ! -d "quality/php-cs-fixer/vendor" ]; then
    composer install --working-dir=quality/php-cs-fixer
fi

# PHP-MESS-DETECTOR requirements
if [ ! -d "quality/php-mess-detector/vendor" ]; then
    composer install --working-dir=quality/php-mess-detector
fi

# PHP-STAN requirements
if [ ! -d "quality/php-stan/vendor" ]; then
    composer install --working-dir=quality/php-stan
fi

git config --global --add safe.directory /var/www

# Let the container stay alive
exec sleep infinity
