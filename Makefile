PHP_BIN      := php
COMPOSER_BIN := composer
BOX_BIN      := box
SHA1SUM		 := sha1sum

.PHONY: build composer-install-dev tests tests-coverage

build: tests
	$(BOX_BIN) compile
	$(SHA1SUM) deptrac.phar > deptrac.version

composer-install-dev:
	$(COMPOSER_BIN) install --optimize-autoloader

tests: composer-install-dev
	$(PHP_BIN) ./vendor/phpunit/phpunit/phpunit -c .
	$(PHP_BIN) deptrac.php analyze examples/Fixture.depfile.yml

tests-coverage: composer-install-dev
	$(PHP_BIN) ./vendor/phpunit/phpunit/phpunit -c . --coverage-html coverage
