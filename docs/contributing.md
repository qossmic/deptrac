# Contributing to Deptrac

There are many ways to contribute to Deptrac, from helping others with their
issues, improving the documentation to fixing bugs & adding new features.

When you want to add a new feature to Deptrac, please make sure to open an issue
first to let others know who is working on it and prevent similar or conflicting
pull requests. We are always happy to expand the possibilities of Deptrac to
better fit the need of anyone who uses it. Before we merge changes, we have to
decide whether we can maintain them without taking away resources needed
elsewhere. Unfortunately, that means we have to reject some change requests.
Opening an issue before you start working on any new feature will make sure that
your merge request can be accepted.

## Requirements

- PHP in version 7.4 or above
- [Composer](https://getcomposer.org/)
- `make`

## Installing tools

You can install all tools needed for developing Deptrac using the Makefile by
running the following command:

```bash
make composer-install
```

## Pipeline

Any merge request must pass our build pipeline which consists of the following:

* Unit Tests for all supported PHP-versions
* Check for coding guidelines
* Static code analysis with phpstan and psalm
* End 2 End-tests, ensuring `deptrac.phar` can be built

You can use the provided Makefile to execute these steps locally. The `make`
command is supported by most major operating systems, but you might need to
install it first. The Makefile will use Composer to install the required tools
like PHPUnit, Psalm or PHPStan. If you don't want to use Composer or the
Makefile you will need to install them yourself.

### Tests

You can run the unit tests locally using the provided Makefile

```
make tests
```

This will run phpunit to make sure the tests pass. We recommend running the
tests once before you make any changes to ensure they work on your system. This
way you can be sure that any failing test is not caused by a pre-existing
problem.

### Code style

You can check if your code changes are in line with our coding guidelines using
php-cs-fixer.

```
make php-cs-check
```

This will show you any code style violations that are also reported on Github.
You can automatically fix them by running:

```
make php-cs-fix
```

### Static code analysis

In order to perform static code analysis for your changes you can run:

```
make phpstan
make psalm
```

We also run a tool called infection for mutation testing:

```
make infection
```

### Build Deptrac

You can build the `deptrac.phar` both to ensure it works, as well as for using
it to analyse your existing projects to see if your changes work as expected.

```bash
make build
```

This will create an executable file `deptrac.phar` in the current directory.
