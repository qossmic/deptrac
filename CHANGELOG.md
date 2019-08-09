# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 0.6.0 - 2019-xx-xx

### Changed

- [#228] improve file exclusion, Thanks to [@smoench]
- [#235] Improve console output of analyze command., Thanks to [@temp]
- [#245] refactor/improve method collector, Thanks to [@smoench]
- [#244] dependency updates, Thanks to [@smoench]
- [#243] anonymous class resolver, Thanks to [@smoench]
- [#242] upgrade box to v3.8, Thanks to [@smoench]
- [#241] class constant resolver, Thanks to [@smoench]
- [#236] split progressbar to its own subscriber, Thanks to [@smoench]
- [#247] refactor inherits resolving, Thanks to [@smoench]
- [#234] upgrade to symfony 4.3, Thanks to [@smoench]
- [#233] upgrade to phpunit 8, Thanks to [@smoench]
- [#232] increase minimum php version to 7.2, Thanks to [@smoench]
- [#246] naming + improvements, Thanks to [@smoench]
- [#224] annotation dependency resolver, Thanks to [@smoench]
- [#248] Update documentation on bool collector to describe actual behaviour, Thanks to [@rpkamp]

### Fixed

- [#230] Fix alignment, Thanks to [@BackEndTea]
- [#223] Fix outdated graphviz download link in README, Thanks to [@LeoVie]

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
