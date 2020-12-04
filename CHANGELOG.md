# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

<!-- changelog-linker -->

## [0.10.1] - 2020-12-04

### Added

- [#418] add psalm pseudo-types, Thanks to [@marcosh]

### Changed

- [#428] Bump nikic/php-parser from 4.10.2 to 4.10.3, Thanks to [@dependabot][bot]
- [#420] update to box v3.9.1, Thanks to [@smoench]
- [#419] update jetbrains/phpstorm-stubs to v2020.2, Thanks to [@smoench]

## [0.10.0] - 2020-11-20

### Added
- [#417] adds PHP 8 support, Thanks to [@smoench]

### Changed

- [#417] changed emulative lexer to fixed one, Thanks to [@smoench]

## [0.9.0] - 2020-10-30

### Added

- [#403] report unmatched skipped violations, Thanks to [@smoench]
- [#395] Introduce BaselineOutputFormatter, Thanks to [@marcelthole]
- [#378] [GraphViz] display depend on count, Thanks to [@smoench]
- [#337] introduce table output formatter, Thanks to [@smoench]
- [#320] Load Collectors by FQCN, Thanks to [@DanielBadura]

### Fixed

- [#399] Fix: Reject rule sets referencing unknown layers, Thanks to [@localheinz]
- [#398] Fix: Reject duplicate layer names, Thanks to [@localheinz]

### Changed

- [#402] github actions show inherit path, Thanks to [@smoench]
- [#401] use composer v2 for CI, Thanks to [@smoench]
- [#400] Enhancement: Mark test classes as final, Thanks to [@localheinz]
- [#397] Enhancement: Enable final_static_access fixer, Thanks to [@localheinz]
- [#396] Simplify formatter options, Thanks to [@smoench]
- [#366] Bump symfony/console from 5.1.4 to 5.1.5
- [#364] Bump symfony/config from 5.1.3 to 5.1.4
- [#363] Bump symfony/event-dispatcher from 5.1.3 to 5.1.4
- [#365] Bump symfony/yaml from 5.1.4 to 5.1.5
- [#411] Bump symfony/options-resolver from 5.1.7 to 5.1.8
- [#367] Bump symfony/config from 5.1.4 to 5.1.5
- [#368] Bump symfony/finder from 5.1.4 to 5.1.5
- [#361] Bump symfony/yaml from 5.1.3 to 5.1.4
- [#369] Bump symfony/dependency-injection from 5.1.4 to 5.1.5
- [#362] Bump symfony/dependency-injection from 5.1.3 to 5.1.4
- [#354] Bump nikic/php-parser from 4.7.0 to 4.8.0
- [#360] Bump symfony/finder from 5.1.3 to 5.1.4
- [#359] Bump symfony/options-resolver from 5.1.3 to 5.1.4
- [#358] Bump symfony/console from 5.1.3 to 5.1.4
- [#357] Bump nikic/php-parser from 4.9.0 to 4.9.1
- [#356] Bump composer/xdebug-handler from 1.4.2 to 1.4.3
- [#355] Bump nikic/php-parser from 4.8.0 to 4.9.0
- [#371] Bump symfony/event-dispatcher from 5.1.4 to 5.1.5
- [#350] Bump phpstan/phpdoc-parser from 0.4.8 to 0.4.9
- [#348] Bump nikic/php-parser from 4.6.0 to 4.7.0
- [#370] Bump symfony/options-resolver from 5.1.4 to 5.1.5
- [#372] Bump phpdocumentor/type-resolver from 1.3.0 to 1.4.0
- [#410] Bump symfony/finder from 5.1.7 to 5.1.8
- [#374] Bump nikic/php-parser from 4.9.1 to 4.10.0
- [#409] Bump symfony/config from 5.1.7 to 5.1.8
- [#408] Bump symfony/event-dispatcher from 5.1.7 to 5.1.8
- [#407] Bump symfony/dependency-injection from 5.1.7 to 5.1.8
- [#406] Bump symfony/console from 5.1.7 to 5.1.8
- [#405] Bump symfony/yaml from 5.1.7 to 5.1.8
- [#404] Bump composer/xdebug-handler from 1.4.3 to 1.4.4
- [#394] Bump symfony/dependency-injection from 5.1.6 to 5.1.7
- [#393] Bump symfony/options-resolver from 5.1.6 to 5.1.7
- [#392] Bump symfony/finder from 5.1.6 to 5.1.7
- [#384] Bump symfony/finder from 5.1.5 to 5.1.6
- [#377] update tools, Thanks to [@smoench]
- [#379] Bump nikic/php-parser from 4.10.0 to 4.10.1
- [#380] Bump symfony/options-resolver from 5.1.5 to 5.1.6
- [#381] Bump symfony/dependency-injection from 5.1.5 to 5.1.6
- [#382] Bump symfony/event-dispatcher from 5.1.5 to 5.1.6
- [#383] Bump symfony/config from 5.1.5 to 5.1.6
- [#385] Bump nikic/php-parser from 4.10.1 to 4.10.2
- [#391] Bump symfony/event-dispatcher from 5.1.6 to 5.1.7
- [#386] Bump symfony/yaml from 5.1.5 to 5.1.6
- [#387] Bump symfony/console from 5.1.5 to 5.1.6
- [#388] Bump symfony/config from 5.1.6 to 5.1.7
- [#389] Bump symfony/console from 5.1.6 to 5.1.7
- [#390] Bump symfony/yaml from 5.1.6 to 5.1.7

## [0.8.2] - 2020-07-24

### Added

- [#338] Add option to report uncovered dependencies for GitHubActionFormatter, Thanks to [@jschaedl]
- [#335] Add missing Collectors into the container, Thanks to [@DanielBadura]

### Changed

- [#347] Bump symfony/finder from 5.1.2 to 5.1.3
- [#346] Bump symfony/console from 5.1.2 to 5.1.3
- [#345] Bump symfony/yaml from 5.1.2 to 5.1.3
- [#344] Bump symfony/dependency-injection from 5.1.2 to 5.1.3
- [#343] Bump symfony/config from 5.1.2 to 5.1.3
- [#342] Bump nikic/php-parser from 4.5.0 to 4.6.0
- [#341] Bump phpdocumentor/type-resolver from 1.1.0 to 1.3.0
- [#340] Bump symfony/options-resolver from 5.1.2 to 5.1.3
- [#339] Bump symfony/event-dispatcher from 5.1.2 to 5.1.3
- [#336] ignore (psalm) pseudo types, Thanks to [@smoench]
- [#334] Change default file to depfile.yaml, Thanks to [@DanielBadura]

## [0.8.1] - 2020-07-10

### Added

- [#321] Add Psalm, Thanks to [@DanielBadura]

### Changed

- [#324] Enable GithubActionsOutputFormatter by default in GithubActions environment, Thanks to [@jschaedl]
- [#323] psalm level 2, Thanks to [@smoench]
- [#322] differentiate between possible use types, Thanks to [@smoench]

## [0.8.0] - 2020-06-19

### Added

- [#314] adds jetbrains/phpstorm-stubs for not blaming about uncovered internal classes, Thanks to [@smoench]
- [#311] Adds uses, extends and inherits collectors., Thanks to [@dbrumann]
- [#307] Add flag --fail-on-uncovered (closes [#306]), Thanks to [@hugochinchilla]
- [#305] Add GitHub Actions Output Formatter, Thanks to [@jtaylor100]

### Changed

- [#316] install tools with phive, Thanks to [@smoench]
- [#315] upgrade to symfony 5.1, Thanks to [@smoench]
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
[@jschaedl]: https://github.com/jschaedl
[@DanielBadura]: https://github.com/DanielBadura
[@marcelthole]: https://github.com/marcelthole
[@marcosh]: https://github.com/marcosh

[#428]: https://github.com/sensiolabs-de/deptrac/pull/428
[#420]: https://github.com/sensiolabs-de/deptrac/pull/420
[#419]: https://github.com/sensiolabs-de/deptrac/pull/419
[#418]: https://github.com/sensiolabs-de/deptrac/pull/418
[#417]: https://github.com/sensiolabs-de/deptrac/pull/417
[#411]: https://github.com/sensiolabs-de/deptrac/pull/411
[#410]: https://github.com/sensiolabs-de/deptrac/pull/410
[#409]: https://github.com/sensiolabs-de/deptrac/pull/409
[#408]: https://github.com/sensiolabs-de/deptrac/pull/408
[#407]: https://github.com/sensiolabs-de/deptrac/pull/407
[#406]: https://github.com/sensiolabs-de/deptrac/pull/406
[#405]: https://github.com/sensiolabs-de/deptrac/pull/405
[#404]: https://github.com/sensiolabs-de/deptrac/pull/404
[#403]: https://github.com/sensiolabs-de/deptrac/pull/403
[#402]: https://github.com/sensiolabs-de/deptrac/pull/402
[#401]: https://github.com/sensiolabs-de/deptrac/pull/401
[#400]: https://github.com/sensiolabs-de/deptrac/pull/400
[#399]: https://github.com/sensiolabs-de/deptrac/pull/399
[#398]: https://github.com/sensiolabs-de/deptrac/pull/398
[#397]: https://github.com/sensiolabs-de/deptrac/pull/397
[#396]: https://github.com/sensiolabs-de/deptrac/pull/396
[#395]: https://github.com/sensiolabs-de/deptrac/pull/395
[#394]: https://github.com/sensiolabs-de/deptrac/pull/394
[#393]: https://github.com/sensiolabs-de/deptrac/pull/393
[#392]: https://github.com/sensiolabs-de/deptrac/pull/392
[#391]: https://github.com/sensiolabs-de/deptrac/pull/391
[#390]: https://github.com/sensiolabs-de/deptrac/pull/390
[#389]: https://github.com/sensiolabs-de/deptrac/pull/389
[#388]: https://github.com/sensiolabs-de/deptrac/pull/388
[#387]: https://github.com/sensiolabs-de/deptrac/pull/387
[#386]: https://github.com/sensiolabs-de/deptrac/pull/386
[#385]: https://github.com/sensiolabs-de/deptrac/pull/385
[#384]: https://github.com/sensiolabs-de/deptrac/pull/384
[#383]: https://github.com/sensiolabs-de/deptrac/pull/383
[#382]: https://github.com/sensiolabs-de/deptrac/pull/382
[#381]: https://github.com/sensiolabs-de/deptrac/pull/381
[#380]: https://github.com/sensiolabs-de/deptrac/pull/380
[#379]: https://github.com/sensiolabs-de/deptrac/pull/379
[#378]: https://github.com/sensiolabs-de/deptrac/pull/378
[#377]: https://github.com/sensiolabs-de/deptrac/pull/377
[#374]: https://github.com/sensiolabs-de/deptrac/pull/374
[#372]: https://github.com/sensiolabs-de/deptrac/pull/372
[#371]: https://github.com/sensiolabs-de/deptrac/pull/371
[#370]: https://github.com/sensiolabs-de/deptrac/pull/370
[#369]: https://github.com/sensiolabs-de/deptrac/pull/369
[#368]: https://github.com/sensiolabs-de/deptrac/pull/368
[#367]: https://github.com/sensiolabs-de/deptrac/pull/367
[#366]: https://github.com/sensiolabs-de/deptrac/pull/366
[#365]: https://github.com/sensiolabs-de/deptrac/pull/365
[#364]: https://github.com/sensiolabs-de/deptrac/pull/364
[#363]: https://github.com/sensiolabs-de/deptrac/pull/363
[#362]: https://github.com/sensiolabs-de/deptrac/pull/362
[#361]: https://github.com/sensiolabs-de/deptrac/pull/361
[#360]: https://github.com/sensiolabs-de/deptrac/pull/360
[#359]: https://github.com/sensiolabs-de/deptrac/pull/359
[#358]: https://github.com/sensiolabs-de/deptrac/pull/358
[#357]: https://github.com/sensiolabs-de/deptrac/pull/357
[#356]: https://github.com/sensiolabs-de/deptrac/pull/356
[#355]: https://github.com/sensiolabs-de/deptrac/pull/355
[#354]: https://github.com/sensiolabs-de/deptrac/pull/354
[#350]: https://github.com/sensiolabs-de/deptrac/pull/350
[#348]: https://github.com/sensiolabs-de/deptrac/pull/348
[#337]: https://github.com/sensiolabs-de/deptrac/pull/337
[#320]: https://github.com/sensiolabs-de/deptrac/pull/320
[#347]: https://github.com/sensiolabs-de/deptrac/pull/347
[#346]: https://github.com/sensiolabs-de/deptrac/pull/346
[#345]: https://github.com/sensiolabs-de/deptrac/pull/345
[#344]: https://github.com/sensiolabs-de/deptrac/pull/344
[#343]: https://github.com/sensiolabs-de/deptrac/pull/343
[#342]: https://github.com/sensiolabs-de/deptrac/pull/342
[#341]: https://github.com/sensiolabs-de/deptrac/pull/341
[#340]: https://github.com/sensiolabs-de/deptrac/pull/340
[#339]: https://github.com/sensiolabs-de/deptrac/pull/339
[#338]: https://github.com/sensiolabs-de/deptrac/pull/338
[#336]: https://github.com/sensiolabs-de/deptrac/pull/336
[#335]: https://github.com/sensiolabs-de/deptrac/pull/335
[#334]: https://github.com/sensiolabs-de/deptrac/pull/334
[#324]: https://github.com/sensiolabs-de/deptrac/pull/324
[#323]: https://github.com/sensiolabs-de/deptrac/pull/323
[#322]: https://github.com/sensiolabs-de/deptrac/pull/322
[#321]: https://github.com/sensiolabs-de/deptrac/pull/321
[#316]: https://github.com/sensiolabs-de/deptrac/pull/316
[#315]: https://github.com/sensiolabs-de/deptrac/pull/315
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

[0.10.1]: https://github.com/sensiolabs-de/deptrac/compare/0.10.0...0.10.1
[0.10.0]: https://github.com/sensiolabs-de/deptrac/compare/0.9.0...0.10.0
[0.9.0]: https://github.com/sensiolabs-de/deptrac/compare/0.8.2...0.9.0
[0.8.2]: https://github.com/sensiolabs-de/deptrac/compare/0.8.1...0.8.2
[0.8.1]: https://github.com/sensiolabs-de/deptrac/compare/0.8.0...0.8.1
[0.8.0]: https://github.com/sensiolabs-de/deptrac/compare/0.7.1...0.8.0
[0.7.1]: https://github.com/sensiolabs-de/deptrac/compare/0.7.0...0.7.1
[0.7.0]: https://github.com/sensiolabs-de/deptrac/compare/0.6.0...0.7.0
[0.6.0]: https://github.com/sensiolabs-de/deptrac/compare/0.5.0...0.6.0
[0.5.0]: https://github.com/sensiolabs-de/deptrac/compare/0.4.0...0.5.0
[0.4.0]: https://github.com/sensiolabs-de/deptrac/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/sensiolabs-de/deptrac/compare/0.2.0...0.3.0
