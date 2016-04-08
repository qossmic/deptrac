.PHONY: build composer-install-dev tess tests-coverage

build: tests
	composer install --no-dev --optimize-autoloader
	box build
	chmod +x deptrac.phar

composer-install-dev:
	composer install --dev --optimize-autoloader

tests: composer-install-dev
	./vendor/phpunit/phpunit/phpunit -c .

tests-coverage: composer-install-dev
	./vendor/phpunit/phpunit/phpunit -c . --coverage-html coverage
