# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## [0.8.0] - 2020-06-xx

### Added

- [#314] adds jetbrains/phpstorm-stubs for not blaming about uncovered internal classes, Thanks to [@smoench]
- [#311] Adds uses, extends and inherits collectors., Thanks to [@dbrumann]
- [#307] Add flag --fail-on-uncovered (closes [#306]), Thanks to [@hugochinchilla]
- [#305] Add GitHub Actions Output Formatter, Thanks to [@jtaylor100]

### Changed

- [#303] update github actions, Thanks to [@smoench]

### Fixed

- [#308] Fixes key for implements Collector, Thanks to [@dbrumann]

## [0.7.1] - 2020-05-04

### Fixed

- [#302] Bugfix: cover more DocBlock types

## [0.7.0] - 2020-05-02

### Added

- [#298] adds implements collector, Thanks to [@smoench]
- [#297] Add doc for MethodCollector and misc, Thanks to [@smoench]
- [#294] adds composer/xdebug-handler, Thanks to [@smoench]
- [#285] report uncovered dependencies, Thanks to [@smoench]
- [#270] add xml formatter, Thanks to [@timglabisch]

### Changed

- [#300] adds file reference builder, Thanks to [@smoench]
- [#296] use php config files, Thanks to [@smoench]
- [#295] upload phar artifact, Thanks to [@smoench]
- [#283] improve analysing performance, Thanks to [@smoench]
- [#278] make AstMap immutable, Thanks to [@smoench]
- [#271] test on PHP 7.4, Thanks to [@smoench]
- [#272] Update README.md, Thanks to [@radimvaculik]
- [#274] use github actions, Thanks to [@smoench]
- [#275] use checkout fetch depth one, Thanks to [@smoench]
- [#276] upgrade to symfony 5.0, Thanks to [@smoench]
- [#281] update actions config, Thanks to [@smoench]
- [#279] resolve file occurrences of dependencies, Thanks to [@smoench]
- [#292] dependency updates, Thanks to [@smoench]
- [#284] update dependencies, Thanks to [@smoench]
- [#286] use latest phpstan version, Thanks to [@smoench]
- [#287] refactor type resolving, Thanks to [@smoench]
- [#289] dependency updates, Thanks to [@smoench]
- [#290] jUnit: report successful + uncovered testcases, Thanks to [@smoench]
- [#266] POC: track uncovered dependencies, Thanks to [@smoench]

### Removed

- [#288] remove banner, Thanks to [@smoench]

## [0.6.0] - 2019-10-18

### Changed

- [#255](https://github.com/sensiolabs-de/deptrac/pull/255) Enhancement: Add return type declarations to closures, Thanks to [@localheinz]
- [#257](https://github.com/sensiolabs-de/deptrac/pull/257) adds missing dependency resolver test, Thanks to [@smoench]
- [#267](https://github.com/sensiolabs-de/deptrac/pull/267) improve tests, Thanks to [@smoench]
- [#262](https://github.com/sensiolabs-de/deptrac/pull/262) dependency updates, Thanks to [@smoench]
- [#261](https://github.com/sensiolabs-de/deptrac/pull/261) Update README.md, Thanks to [@dbrumann]
- [#259](https://github.com/sensiolabs-de/deptrac/pull/259) phpstan level max, Thanks to [@smoench]
- [#258](https://github.com/sensiolabs-de/deptrac/pull/258) Enhancement: Throw exception when configuration can be parsed as yaml, but does not contain array, Thanks to [@localheinz]
- [#263](https://github.com/sensiolabs-de/deptrac/pull/263) phpstan - inferPrivatePropertyTypeFromConstructor, Thanks to [@smoench]
- [#264](https://github.com/sensiolabs-de/deptrac/pull/264) simplify console output mode (verbose), Thanks to [@smoench]
- [#254](https://github.com/sensiolabs-de/deptrac/pull/254) Enhancement: Enable static_lambda fixer, Thanks to [@localheinz]
- [#253](https://github.com/sensiolabs-de/deptrac/pull/253) Enhancement: Keep rules sorted in .php_cs.dist, Thanks to [@localheinz]
- [#252](https://github.com/sensiolabs-de/deptrac/pull/252) Enhancement: Introduce temporary variable, Thanks to [@localheinz]
- [#251](https://github.com/sensiolabs-de/deptrac/pull/251) Enhancement: Update phpstan/phpstan, Thanks to [@localheinz]
- [#250](https://github.com/sensiolabs-de/deptrac/pull/250) use event classes for emitted and flattened dependencies, Thanks to [@smoench]
- [#249](https://github.com/sensiolabs-de/deptrac/pull/249) refactor dependencies resolution, Thanks to [@smoench]
- [#247](https://github.com/sensiolabs-de/deptrac/pull/247) refactor inherits resolving, Thanks to [@smoench]
- [#256](https://github.com/sensiolabs-de/deptrac/pull/256) Enhancement: Throw exception when configuration cannot be parsed as yaml, Thanks to [@localheinz]
- [#228](https://github.com/sensiolabs-de/deptrac/pull/228) improve file exclusion, Thanks to [@smoench]
- [#235](https://github.com/sensiolabs-de/deptrac/pull/235) Improve console output of analyze command., Thanks to [@temp]
- [#245](https://github.com/sensiolabs-de/deptrac/pull/245) refactor/improve method collector, Thanks to [@smoench]
- [#244](https://github.com/sensiolabs-de/deptrac/pull/244) dependency updates, Thanks to [@smoench]
- [#243](https://github.com/sensiolabs-de/deptrac/pull/243) anonymous class resolver, Thanks to [@smoench]
- [#242](https://github.com/sensiolabs-de/deptrac/pull/242) upgrade box to v3.8, Thanks to [@smoench]
- [#241](https://github.com/sensiolabs-de/deptrac/pull/241) class constant resolver, Thanks to [@smoench]
- [#236](https://github.com/sensiolabs-de/deptrac/pull/236) split progressbar to its own subscriber, Thanks to [@smoench]
- [#247](https://github.com/sensiolabs-de/deptrac/pull/247) refactor inherits resolving, Thanks to [@smoench]
- [#234](https://github.com/sensiolabs-de/deptrac/pull/234) upgrade to symfony 4.3, Thanks to [@smoench]
- [#233](https://github.com/sensiolabs-de/deptrac/pull/233) upgrade to phpunit 8, Thanks to [@smoench]
- [#232](https://github.com/sensiolabs-de/deptrac/pull/232) increase minimum php version to 7.2, Thanks to [@smoench]
- [#246](https://github.com/sensiolabs-de/deptrac/pull/246) naming + improvements, Thanks to [@smoench]
- [#224](https://github.com/sensiolabs-de/deptrac/pull/224) annotation dependency resolver, Thanks to [@smoench]
- [#248](https://github.com/sensiolabs-de/deptrac/pull/248) Update documentation on bool collector to describe actual behaviour, Thanks to [@rpkamp]

### Fixed

- [#265](https://github.com/sensiolabs-de/deptrac/pull/265) Bugfix: classes in other namespaces are resolved in same namespace, Thanks to [@smoench]
- [#227](https://github.com/sensiolabs-de/deptrac/pull/227) Fix: Remove non-applicable exclude configuration, Thanks to [@localheinz]
- [#230](https://github.com/sensiolabs-de/deptrac/pull/230) Fix alignment, Thanks to [@BackEndTea]
- [#223](https://github.com/sensiolabs-de/deptrac/pull/223) Fix outdated graphviz download link in README, Thanks to [@LeoVie]

## [0.5.0] - 2019-03-15

### Added

- [#219](https://github.com/sensiolabs-de/deptrac/pull/219) added input parameter option for cache file

### Changed

- [#215](https://github.com/sensiolabs-de/deptrac/pull/215) Enhancement: Apply `@PHPUnit60Migration:risky` ruleset

- [#214](https://github.com/sensiolabs-de/deptrac/pull/214) Enhancement: Update phpstan/phpstan

### Fixed

- [#216](https://github.com/sensiolabs-de/deptrac/pull/216) Fix: Remove sudo configuration

- [#213](https://github.com/sensiolabs-de/deptrac/pull/213) Enhancement: Reference phpunit.xsd as installed with composer

- [#211](https://github.com/sensiolabs-de/deptrac/pull/211) improved caching mechanism

- [#210](https://github.com/sensiolabs-de/deptrac/pull/210) don't apply dependencies from prev classes to current class when file contains more than one class

## [0.4.0] - 2019-01-11

### Added

- [#195](https://github.com/sensiolabs-de/deptrac/pull/195) chaching parsed files

- [#200](https://github.com/sensiolabs-de/deptrac/pull/200) skip class dependency violation (@torinaki)

### Changed

- [#197](https://github.com/sensiolabs-de/deptrac/pull/197) use progress bar instead printing dots

- [#190](https://github.com/sensiolabs-de/deptrac/pull/190) added several nullable and void type-hints

### Removed

- [#190](https://github.com/sensiolabs-de/deptrac/pull/190) removed support for PHP <7.1

## [0.3.0] - 2018-11-05

### Added

- [#160](https://github.com/sensiolabs-de/deptrac/pull/160) new ClassNameRegexCollector was added

- [#167](https://github.com/sensiolabs-de/deptrac/pull/167) Added JUnit report formatter.

### Changed

- [#179](https://github.com/sensiolabs-de/deptrac/pull/179) disabled JUnitFormatter and GraphizFormatter by default

### Removed

- [#177](https://github.com/sensiolabs-de/deptrac/pull/177) Removed the self updating mechanism.

## [0.2.0] - 2018-03-24

### Added

- Added DirectoryCollector.

### Removed

- Removed support for PHP < 7.0 and HHVM.

[@smoench]: https://github.com/smoench
[@localheinz]: https://github.com/localheinz
[@dbrumann]: https://github.com/dbrumann
[@temp]: https://github.com/temp
[@rpkamp]: https://github.com/rpkamp
[@LeoVie]: https://github.com/LeoVie
[@BackEndTea]: https://github.com/BackEndTea
[@timglabisch]: https://github.com/timglabisch
[@radimvaculik]: https://github.com/radimvaculik
[@jtaylor100]: https://github.com/jtaylor100
[@hugochinchilla]: https://github.com/hugochinchilla

[#314]: https://github.com/sensiolabs-de/deptrac/pull/314
[#311]: https://github.com/sensiolabs-de/deptrac/pull/311
[#308]: https://github.com/sensiolabs-de/deptrac/pull/308
[#307]: https://github.com/sensiolabs-de/deptrac/pull/307
[#306]: https://github.com/sensiolabs-de/deptrac/pull/306
[#305]: https://github.com/sensiolabs-de/deptrac/pull/305
[#303]: https://github.com/sensiolabs-de/deptrac/pull/303
[#302]: https://github.com/sensiolabs-de/deptrac/pull/302
[#300]: https://github.com/sensiolabs-de/deptrac/pull/300
[#298]: https://github.com/sensiolabs-de/deptrac/pull/298
[#297]: https://github.com/sensiolabs-de/deptrac/pull/297
[#296]: https://github.com/sensiolabs-de/deptrac/pull/296
[#295]: https://github.com/sensiolabs-de/deptrac/pull/295
[#294]: https://github.com/sensiolabs-de/deptrac/pull/294
[#292]: https://github.com/sensiolabs-de/deptrac/pull/292
[#290]: https://github.com/sensiolabs-de/deptrac/pull/290
[#289]: https://github.com/sensiolabs-de/deptrac/pull/289
[#288]: https://github.com/sensiolabs-de/deptrac/pull/288
[#287]: https://github.com/sensiolabs-de/deptrac/pull/287
[#286]: https://github.com/sensiolabs-de/deptrac/pull/286
[#285]: https://github.com/sensiolabs-de/deptrac/pull/285
[#284]: https://github.com/sensiolabs-de/deptrac/pull/284
[#283]: https://github.com/sensiolabs-de/deptrac/pull/283
[#281]: https://github.com/sensiolabs-de/deptrac/pull/281
[#279]: https://github.com/sensiolabs-de/deptrac/pull/279
[#278]: https://github.com/sensiolabs-de/deptrac/pull/278
[#276]: https://github.com/sensiolabs-de/deptrac/pull/276
[#275]: https://github.com/sensiolabs-de/deptrac/pull/275
[#274]: https://github.com/sensiolabs-de/deptrac/pull/274
[#272]: https://github.com/sensiolabs-de/deptrac/pull/272
[#271]: https://github.com/sensiolabs-de/deptrac/pull/271
[#270]: https://github.com/sensiolabs-de/deptrac/pull/270
[#266]: https://github.com/sensiolabs-de/deptrac/pull/266

[0.7.1]: https://github.com/sensiolabs-de/deptrac/compare/0.7.0...0.7.1
[0.7.0]: https://github.com/sensiolabs-de/deptrac/compare/0.6.0...0.7.0
[0.6.0]: https://github.com/sensiolabs-de/deptrac/compare/0.5.0...0.6.0
[0.5.0]: https://github.com/sensiolabs-de/deptrac/compare/0.4.0...0.5.0
[0.4.0]: https://github.com/sensiolabs-de/deptrac/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/sensiolabs-de/deptrac/compare/0.2.0...0.3.0
[0.8.0]: https://github.com/sensiolabs-de/deptrac/compare/0.7.1...0.8.0
