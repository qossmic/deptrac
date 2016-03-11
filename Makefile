tests:
	./vendor/phpunit/phpunit/phpunit -c .

build: tests
	composer install --no-dev --optimize-autoloader
	box build
	chmod +x deptrac.phar
	composer install --dev --optimize-autoloader

tests_coverage:
	./vendor/phpunit/phpunit/phpunit -c . --coverage-html coverage

update_ast_runner:
	composer update sensiolabs-de/astrunner

integration_symfony:
	rm -rf /tmp/08137051b && git clone --depth 1 git@github.com:symfony/symfony.git /tmp/08137051b
	php deptrac.php analyze examples/symfony_depfile.yml # 4780 Violations

integration_sylius:
	rm -rf /tmp/c1023228a && git clone --depth 1 git@github.com:Sylius/Sylius.git /tmp/c1023228a
	php deptrac.php analyze examples/sylius_depfile.yml # 7047 Violations
