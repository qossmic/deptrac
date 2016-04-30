.PHONY: build composer-install-dev tess tests-coverage

build: tests
	composer install --no-dev --optimize-autoloader
	box build
	chmod +x deptrac.phar

composer-install-dev:
	COMPOSER-CMD := $(composer)
	ifndef COMPOSER-CMD
		$(error "Composer is not available globally.")
	endif
	COMPOSER-CMD install --dev --optimize-autoloader

tests: composer-install-dev
	./vendor/phpunit/phpunit/phpunit -c .

tests-coverage: composer-install-dev
	./vendor/phpunit/phpunit/phpunit -c . --coverage-html coverage
