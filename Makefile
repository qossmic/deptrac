BOX_BIN := ./vendor/bin/box
COMPOSER_BIN := composer
PHP_BIN := php
PHP_CS_FIXER_BIN := ./vendor/bin/php-cs-fixer
PHPSTAN_BIN	:= ./vendor/bin/phpstan
PSALM_BIN	:= ./vendor/bin/psalm
PHPUNIT_BIN	:= ./vendor/bin/phpunit
INFECTION_BIN	:= ./vendor/bin/infection

.PHONY: build composer-install tests tests-coverage gpg php-cs-check php-cs-fix phpstan

build: tests
	$(BOX_BIN) compile

composer-install:
	$(COMPOSER_BIN) install --no-interaction --no-progress --no-suggest --optimize-autoloader

tests: composer-install
	$(PHPUNIT_BIN) -c .
	$(PHP_BIN) deptrac.php analyse --config-file=docs/examples/Fixture.depfile.yaml --no-cache

tests-coverage: composer-install
	$(PHPUNIT_BIN) -c . --coverage-html coverage

php-cs-check:																	## run cs fixer (dry-run)
	$(PHP_CS_FIXER_BIN) fix --allow-risky=yes --diff --using-cache=no --verbose --dry-run

php-cs-fix:																		## run cs fixer
	$(PHP_CS_FIXER_BIN) fix --allow-risky=yes

phpstan:
	$(PHPSTAN_BIN) analyse --memory-limit=256M

deptrac:
	$(PHP_BIN) deptrac.php analyse --no-progress --ansi

psalm:
	$(PSALM_BIN)

infection: composer-install
	$(INFECTION_BIN) --threads=$(shell nproc || sysctl -n hw.ncpu || 1) --test-framework-options='--testsuite=Tests' --only-covered --min-msi=80

gpg:
	gpg --detach-sign --armor --default-key 41DDE07547459FAECFA17813B8F640134AB1782E --output deptrac.phar.asc deptrac.phar
	gpg --verify deptrac.phar.asc deptrac.phar

#generate-changelog:
#	gem install github_changelog_generator
#	github_changelog_generator -u qossmic -p deptrac --no-issues --future-release <version>
