tests:
	./vendor/phpunit/phpunit/phpunit -c .

build: tests
	box build

tests_coverage:
	./vendor/phpunit/phpunit/phpunit -c . --coverage-html coverage

update_ast_runner:
	composer update sensiolabs-de/astrunner
