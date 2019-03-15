# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 0.5.0 - 2019-03-15

### Added

- [#219](https://github.com/sensiolabs-de/deptrac/pull/219) added input parameter option for cache file

### Changed

- [#215](https://github.com/sensiolabs-de/deptrac/pull/215) Enhancement: Apply @PHPUnit60Migration:risky ruleset

- [#214](https://github.com/sensiolabs-de/deptrac/pull/214) Enhancement: Update phpstan/phpstan

### Fixed

- [#216](https://github.com/sensiolabs-de/deptrac/pull/216) Fix: Remove sudo configuration

- [#213](https://github.com/sensiolabs-de/deptrac/pull/213) Enhancement: Reference phpunit.xsd as installed with composer

- [#211](https://github.com/sensiolabs-de/deptrac/pull/211) improved caching mechanism

- [#210](https://github.com/sensiolabs-de/deptrac/pull/210) don't apply dependencies from prev classes to current class when file contains more than one class

## 0.4.0 - 2019-01-11

### Added

- [#195](https://github.com/sensiolabs-de/deptrac/pull/195) chaching parsed files

- [#200](https://github.com/sensiolabs-de/deptrac/pull/200) skip class dependency violation (@torinaki)

### Changed

- [#197](https://github.com/sensiolabs-de/deptrac/pull/197) use progress bar instead printing dots

- [#190](https://github.com/sensiolabs-de/deptrac/pull/190) added several nullable and void type-hints

### Removed

- [#190](https://github.com/sensiolabs-de/deptrac/pull/190) removed support for PHP <7.1

## 0.3.0 - 2018-11-05

### Added

- [#160](https://github.com/sensiolabs-de/deptrac/pull/160) new ClassNameRegexCollector was added

- [#167](https://github.com/sensiolabs-de/deptrac/pull/167) Added JUnit report formatter.

### Changed

- [#179](https://github.com/sensiolabs-de/deptrac/pull/179) disabled JUnitFormatter and GraphizFormatter by default

### Removed

- [#177](https://github.com/sensiolabs-de/deptrac/pull/177) Removed the self updating mechanism.

## 0.2.0 - 2018-03-24

### Added

- Added DirectoryCollector.

### Removed

- Removed support for PHP < 7.0 and HHVM.
