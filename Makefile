PHP_BIN      := php
COMPOSER_BIN := composer
BOX_BIN      := box

.PHONY: build composer-install-dev tests tests-coverage gpg php-cs-check php-cs-fix phpstan

build: tests
	$(BOX_BIN) compile

composer-install-dev:
	$(COMPOSER_BIN) install --optimize-autoloader

tests: composer-install-dev
	$(PHP_BIN) ./vendor/phpunit/phpunit/phpunit -c .
	$(PHP_BIN) deptrac.php analyze examples/Fixture.depfile.yml --no-cache

tests-coverage: composer-install-dev
	$(PHP_BIN) ./vendor/phpunit/phpunit/phpunit -c . --coverage-html coverage

php-cs-check:																	## run cs fixer (dry-run)
	PHP_CS_FIXER_FUTURE_MODE=1 php-cs-fixer fix --allow-risky=yes --diff --dry-run

php-cs-fix:																		## run cs fixer
	PHP_CS_FIXER_FUTURE_MODE=1 php-cs-fixer fix --allow-risky=yes

phpstan:
	phpstan analyse

gpg:
	gpg --detach-sign --armor --output deptrac.phar.asc deptrac.phar
	gpg --verify deptrac.phar.asc deptrac.phar
