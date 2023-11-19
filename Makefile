BOX_BIN := build/box.phar
COMPOSER_BIN := composer
PHP_BIN := php
PHP_CS_FIXER_BIN := ./vendor/bin/php-cs-fixer
PHPSTAN_BIN	:= ./vendor/bin/phpstan
PSALM_BIN	:= ./vendor/bin/psalm
PHPUNIT_BIN	:= ./vendor/bin/phpunit
INFECTION_BIN	:= ./vendor/bin/roave-infection-static-analysis-plugin

.PHONY: help
help: ## Displays list of available targets with their descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'


.PHONY: build
build: tests ## Runs tests and creates the phar-binary
	$(BOX_BIN) compile

.PHONY: composer-install
composer-install: ## Installs dependencies
	$(COMPOSER_BIN) install --no-interaction --no-progress -a --ansi

.PHONY: deptrac
deptrac: ## Analyses own architecture using the default config confile
	./deptrac analyse -c deptrac.config.php --cache-file=./.cache/deptrac.cache --no-progress --ansi

#generate-changelog: ## Generates a changelog file based on changes compared to remote origin
#	gem install github_changelog_generator
#	github_changelog_generator -u qossmic -p deptrac --no-issues --future-release <version>

.PHONY: gpg
gpg: ## Signs release with default GPG key "4AB1782E"
	gpg --detach-sign --armor --default-key 41DDE07547459FAECFA17813B8F640134AB1782E --output deptrac.phar.asc deptrac.phar
	gpg --verify deptrac.phar.asc deptrac.phar

.PHONY: infection
infection: composer-install ## Runs mutation tests
	$(INFECTION_BIN) --threads=$(shell nproc || sysctl -n hw.ncpu || 1) --test-framework-options='--testsuite=Tests' --only-covered --min-msi=85 --psalm-config=psalm.xml

.PHONY: php-cs-check
php-cs-check: ## Checks for code style violation
	$(PHP_CS_FIXER_BIN) fix --allow-risky=yes --diff --using-cache=no --verbose --dry-run

.PHONY: php-cs-fix
php-cs-fix: ## Fixes any found code style violation
	$(PHP_CS_FIXER_BIN) fix --allow-risky=yes

.PHONY: phpstan
phpstan: ## Performs static code analysis using phpstan
	$(PHPSTAN_BIN) analyse --memory-limit=256M

.PHONY: psalm
psalm: ## Performs static code analysis using psalm
	$(PSALM_BIN)

.PHONY: tests-coverage
tests-coverage: composer-install ## Runs tests and generate an html coverage report
	XDEBUG_MODE=coverage $(PHPUNIT_BIN) -c . --coverage-html coverage

.PHONY: tests
tests: composer-install ## Runs tests followed by a very basic e2e-test
	$(PHPUNIT_BIN) -c .
	./deptrac analyse --config-file=docs/examples/Fixture.depfile.yaml --no-cache
