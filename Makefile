tests:
	./vendor/phpunit/phpunit/phpunit -c .

build: tests
	composer install --no-dev --optimize-autoloader
	box build
	composer install --dev --no-dev --optimize-autoloader

tests_coverage:
	./vendor/phpunit/phpunit/phpunit -c . --coverage-html coverage

update_ast_runner:
	composer update sensiolabs-de/astrunner
