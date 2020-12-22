BOX_BIN := ./tools/box
COMPOSER_BIN := composer
PHP_BIN := php
PHIVE_BIN := phive
PHP_CS_FIXER_BIN := ./tools/php-cs-fixer
PHPSTAN_BIN	:= ./tools/phpstan
PSALM_BIN	:= ./tools/psalm
PHPUNIT_BIN	:= ./tools/phpunit.phar
INFECTION_BIN	:= ./tools/infection

.PHONY: build tools-install composer-install tests tests-coverage gpg php-cs-check php-cs-fix phpstan

build: tools-install tests
	$(BOX_BIN) compile

tools-install:
	gpg --keyserver hkps://keyserver.ubuntu.com --receive-keys E82B2FB314E9906E 4AA394086372C20A 8E730BA25823D8B5 CF1A108D0E7AE720 8A03EA3B385DBAA1 C5095986493B4AA0
	$(PHIVE_BIN) --no-progress install --copy --trust-gpg-keys E82B2FB314E9906E,4AA394086372C20A,8E730BA25823D8B5,CF1A108D0E7AE720,8A03EA3B385DBAA1,C5095986493B4AA0 --force-accept-unsigned

composer-install:
	$(COMPOSER_BIN) install --optimize-autoloader

tests: composer-install
	$(PHPUNIT_BIN) -c .
	$(PHP_BIN) deptrac.php analyze examples/Fixture.depfile.yaml --no-cache

tests-coverage: composer-install
	$(PHPUNIT_BIN) -c . --coverage-html coverage

php-cs-check:																	## run cs fixer (dry-run)
	PHP_CS_FIXER_FUTURE_MODE=1 $(PHP_CS_FIXER_BIN) fix --allow-risky=yes --diff --diff-format=udiff --using-cache=no --verbose --dry-run

php-cs-fix:																		## run cs fixer
	PHP_CS_FIXER_FUTURE_MODE=1 $(PHP_CS_FIXER_BIN) fix --allow-risky=yes

phpstan:
	$(PHPSTAN_BIN) analyse

psalm:
	$(PSALM_BIN) analyse

infection: tools-install composer-install
	$(INFECTION_BIN) --threads=$(shell nproc || sysctl -n hw.ncpu || 1) --test-framework-options='--testsuite=Tests' --only-covered --min-msi=85

gpg:
	gpg --detach-sign --armor --output deptrac.phar.asc deptrac.phar
	gpg --verify deptrac.phar.asc deptrac.phar
