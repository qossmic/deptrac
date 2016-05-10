PHP_BIN      := php
COMPOSER_BIN := composer
BOX_BIN      := box

.PHONY: build composer-install-dev tests tests-coverage

build: tests
	$(COMPOSER_BIN) install --no-dev --optimize-autoloader
	$(BOX_BIN) build
	chmod +x deptrac.phar

composer-install-dev:
	$(COMPOSER_BIN) install --optimize-autoloader

tests: composer-install-dev
	$(PHP_BIN) ./vendor/phpunit/phpunit/phpunit -c .

tests-coverage: composer-install-dev
	$(PHP_BIN) ./vendor/phpunit/phpunit/phpunit -c . --coverage-html coverage
