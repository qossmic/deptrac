rm -rf deptrac-build
composer install -a --no-dev

php -dmemory_limit=-1 build/php-scoper.phar add-prefix \
    -c scoper.inc.php --working-dir . \
    config internal src vendor deptrac deptrac.config.php deptrac.php

cp composer.* deptrac-build/
cp deptrac.yaml deptrac-build/

cd deptrac-build && composer dump -a --dev

echo 'RUN self test'
./deptrac -c deptrac.config.php
