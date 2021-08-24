# Changelog

## [0.15.1](https://github.com/qossmic/deptrac/tree/0.15.1) (2021-08-24)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.15.0...0.15.1)

**Closed issues:**

- unknown collector type "superglobal" [\#671](https://github.com/qossmic/deptrac/issues/671)

**Merged pull requests:**

- Add superglobal collector to services.php [\#672](https://github.com/qossmic/deptrac/pull/672) ([patrickkusebauch](https://github.com/patrickkusebauch))

## [0.15.0](https://github.com/qossmic/deptrac/tree/0.15.0) (2021-08-20)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.14.1...0.15.0)

**Implemented enhancements:**

- `RulesetEngine` should only use `ConfigurationRuleset` [\#615](https://github.com/qossmic/deptrac/issues/615)

**Closed issues:**

- Incorrect merge of `formatters.graphviz.hidden_layers` from multiple files [\#668](https://github.com/qossmic/deptrac/issues/668)
- Fix deptrac-violations introduced by renaming [\#664](https://github.com/qossmic/deptrac/issues/664)
- print yaml error [\#661](https://github.com/qossmic/deptrac/issues/661)
- Update debug commands [\#638](https://github.com/qossmic/deptrac/issues/638)
- Docs: change generated image for grapviz formatter for groups [\#636](https://github.com/qossmic/deptrac/issues/636)
- Allow only DomainEvents dependency across bounded contexts [\#633](https://github.com/qossmic/deptrac/issues/633)
- Multiple rule sets [\#632](https://github.com/qossmic/deptrac/issues/632)
- Groups: use group names instead of numbers [\#597](https://github.com/qossmic/deptrac/issues/597)
- support procedural php files [\#594](https://github.com/qossmic/deptrac/issues/594)
- throw proper error on invalid regex [\#593](https://github.com/qossmic/deptrac/issues/593)
- Internal - Refactor AstRunner to support parsing more than classes [\#582](https://github.com/qossmic/deptrac/issues/582)
- Fix naming inconsistencies analyze vs. analyse [\#575](https://github.com/qossmic/deptrac/issues/575)
- Scan scripts without classes [\#331](https://github.com/qossmic/deptrac/issues/331)

**Merged pull requests:**

- Update dependencies [\#670](https://github.com/qossmic/deptrac/pull/670) ([dbrumann](https://github.com/dbrumann))
- fixed bug in merging hidden\_layers [\#669](https://github.com/qossmic/deptrac/pull/669) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Make library usable as is without shim [\#666](https://github.com/qossmic/deptrac/pull/666) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Fix master [\#665](https://github.com/qossmic/deptrac/pull/665) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Report yaml syntax errors in more detail [\#662](https://github.com/qossmic/deptrac/pull/662) ([staabm](https://github.com/staabm))
- Run deptrac as part of the test suite [\#648](https://github.com/qossmic/deptrac/pull/648) ([dbrumann](https://github.com/dbrumann))
- Debug commands [\#647](https://github.com/qossmic/deptrac/pull/647) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Ruleset config refactor [\#646](https://github.com/qossmic/deptrac/pull/646) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Eat your own dog food \(use deptrac on deptrac\) [\#645](https://github.com/qossmic/deptrac/pull/645) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Rename analyze to analyse for builds. [\#644](https://github.com/qossmic/deptrac/pull/644) ([dbrumann](https://github.com/dbrumann))
- Cleanup [\#643](https://github.com/qossmic/deptrac/pull/643) ([patrickkusebauch](https://github.com/patrickkusebauch))
- analyze -\> analyse [\#641](https://github.com/qossmic/deptrac/pull/641) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Update docs for graphviz formatter [\#640](https://github.com/qossmic/deptrac/pull/640) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Support for file, function and superglobal tokens [\#634](https://github.com/qossmic/deptrac/pull/634) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Bump nikic/php-parser from 4.10.5 to 4.11.0 [\#630](https://github.com/qossmic/deptrac/pull/630) ([dependabot[bot]](https://github.com/apps/dependabot))
- new GraphVizOutputFormatter [\#626](https://github.com/qossmic/deptrac/pull/626) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Internal refactor to support more token types - part 1/??? [\#602](https://github.com/qossmic/deptrac/pull/602) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Regex validation [\#596](https://github.com/qossmic/deptrac/pull/596) ([patrickkusebauch](https://github.com/patrickkusebauch))

## [0.14.1](https://github.com/qossmic/deptrac/tree/0.14.1) (2021-07-04)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.14.0...0.14.1)

**Closed issues:**

- Tests: E2E test for transitive dependencies [\#619](https://github.com/qossmic/deptrac/issues/619)
- Docs: Transitive dependencies docs dropped with docs refactor [\#617](https://github.com/qossmic/deptrac/issues/617)
- Bool Collector: Multiple Class Layers [\#616](https://github.com/qossmic/deptrac/issues/616)
- Bug: .editorconfig breaks TableOutputFormatterTest [\#614](https://github.com/qossmic/deptrac/issues/614)
- bug: deptrac doesn't understand imported namespaces. [\#609](https://github.com/qossmic/deptrac/issues/609)
- no errors are found if the project name matches `exclude_files` [\#600](https://github.com/qossmic/deptrac/issues/600)
- strange error if specify single file in config [\#599](https://github.com/qossmic/deptrac/issues/599)
- GPG public key not found on keyservers \(phive install/update\) [\#598](https://github.com/qossmic/deptrac/issues/598)
- Can we read the rules somehow from composer.json [\#218](https://github.com/qossmic/deptrac/issues/218)

**Merged pull requests:**

- editorconfig: Ignore trailing whitespace in tests [\#629](https://github.com/qossmic/deptrac/pull/629) ([dbrumann](https://github.com/dbrumann))
- Doc Improvement: Add linkable headlines to features [\#627](https://github.com/qossmic/deptrac/pull/627) ([dbrumann](https://github.com/dbrumann))
- Add e2e test for transitive dependencies [\#623](https://github.com/qossmic/deptrac/pull/623) ([dbrumann](https://github.com/dbrumann))
- Replace changelog-linker with github\_changelog\_generator [\#622](https://github.com/qossmic/deptrac/pull/622) ([dbrumann](https://github.com/dbrumann))
- Add transitive deps to depfile docs [\#621](https://github.com/qossmic/deptrac/pull/621) ([dbrumann](https://github.com/dbrumann))
- Add example for transitive dependencies. [\#620](https://github.com/qossmic/deptrac/pull/620) ([dbrumann](https://github.com/dbrumann))
- BUGFIX: Forgotten config validation for transitive dependencies [\#618](https://github.com/qossmic/deptrac/pull/618) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Fix things missed by rector [\#613](https://github.com/qossmic/deptrac/pull/613) ([dbrumann](https://github.com/dbrumann))
- Bump symfony/dependency-injection from 5.3.2 to 5.3.3 [\#612](https://github.com/qossmic/deptrac/pull/612) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/config from 5.3.2 to 5.3.3 [\#611](https://github.com/qossmic/deptrac/pull/611) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/yaml from 5.3.2 to 5.3.3 [\#610](https://github.com/qossmic/deptrac/pull/610) ([dependabot[bot]](https://github.com/apps/dependabot))
- Note about piping debug commands [\#608](https://github.com/qossmic/deptrac/pull/608) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Update formatters.md [\#607](https://github.com/qossmic/deptrac/pull/607) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Update depfile.md [\#606](https://github.com/qossmic/deptrac/pull/606) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Automatic upgrade of code done with rectorphp/rector [\#605](https://github.com/qossmic/deptrac/pull/605) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Upgrade PHP requirements [\#604](https://github.com/qossmic/deptrac/pull/604) ([dbrumann](https://github.com/dbrumann))
- Update symfony dependencies [\#595](https://github.com/qossmic/deptrac/pull/595) ([dbrumann](https://github.com/dbrumann))
- bugfix in propagating analyzer config [\#588](https://github.com/qossmic/deptrac/pull/588) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Refactored debug output [\#587](https://github.com/qossmic/deptrac/pull/587) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Readme: link the CollectorInterface [\#584](https://github.com/qossmic/deptrac/pull/584) ([staabm](https://github.com/staabm))
- Support for attributes [\#583](https://github.com/qossmic/deptrac/pull/583) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Transitive dependencies [\#579](https://github.com/qossmic/deptrac/pull/579) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Basic template type and generic support [\#578](https://github.com/qossmic/deptrac/pull/578) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Update composer dependency for phpdocparser [\#574](https://github.com/qossmic/deptrac/pull/574) ([patrickkusebauch](https://github.com/patrickkusebauch))
- JUnit output formatter - unmatched skipped violations [\#573](https://github.com/qossmic/deptrac/pull/573) ([patrickkusebauch](https://github.com/patrickkusebauch))
- List unassigned classes [\#572](https://github.com/qossmic/deptrac/pull/572) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Not counting use statements [\#571](https://github.com/qossmic/deptrac/pull/571) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Subgraphs and groups [\#570](https://github.com/qossmic/deptrac/pull/570) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Output configuration - Hidden layers [\#567](https://github.com/qossmic/deptrac/pull/567) ([patrickkusebauch](https://github.com/patrickkusebauch))
- Restructure docs. [\#566](https://github.com/qossmic/deptrac/pull/566) ([dbrumann](https://github.com/dbrumann))
- Switch default branch to main [\#565](https://github.com/qossmic/deptrac/pull/565) ([dbrumann](https://github.com/dbrumann))
- Update Symfony dependencies [\#564](https://github.com/qossmic/deptrac/pull/564) ([dbrumann](https://github.com/dbrumann))
- fix randomly failing test in LayerAnalyerTest [\#563](https://github.com/qossmic/deptrac/pull/563) ([smoench](https://github.com/smoench))
- Add JSON formatter [\#551](https://github.com/qossmic/deptrac/pull/551) ([oldy777](https://github.com/oldy777))
- Bump composer/xdebug-handler from 2.0.0 to 2.0.1 [\#544](https://github.com/qossmic/deptrac/pull/544) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump nikic/php-parser from 4.10.4 to 4.10.5 [\#543](https://github.com/qossmic/deptrac/pull/543) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/yaml from 5.2.5 to 5.2.7 [\#542](https://github.com/qossmic/deptrac/pull/542) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/config from 5.2.4 to 5.2.7 [\#541](https://github.com/qossmic/deptrac/pull/541) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/dependency-injection from 5.2.6 to 5.2.7 [\#540](https://github.com/qossmic/deptrac/pull/540) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/console from 5.2.6 to 5.2.7 [\#539](https://github.com/qossmic/deptrac/pull/539) ([dependabot[bot]](https://github.com/apps/dependabot))

## [0.14.0](https://github.com/qossmic/deptrac/tree/0.14.0) (2021-06-23)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.13.0...0.14.0)

**Implemented enhancements:**

- Report class definitions without layers [\#546](https://github.com/qossmic/deptrac/issues/546)
- Recognizing @template T phpdoc [\#535](https://github.com/qossmic/deptrac/issues/535)
- Add support for attributes [\#375](https://github.com/qossmic/deptrac/issues/375)

**Fixed bugs:**

- Rule with null is interpreted as empty string [\#580](https://github.com/qossmic/deptrac/issues/580)

**Closed issues:**

- Make debug output more friendly for piping into other commands [\#581](https://github.com/qossmic/deptrac/issues/581)
- Count use statements [\#569](https://github.com/qossmic/deptrac/issues/569)
- Add unmatched skipped violations to junit report [\#568](https://github.com/qossmic/deptrac/issues/568)
- Help needed for configuring a Domain-based project [\#555](https://github.com/qossmic/deptrac/issues/555)
- Phar Composer package [\#537](https://github.com/qossmic/deptrac/issues/537)
- Partial use statements [\#536](https://github.com/qossmic/deptrac/issues/536)
- PHP 8 support [\#534](https://github.com/qossmic/deptrac/issues/534)
- Directory collector define depth or exclude subdirectories [\#533](https://github.com/qossmic/deptrac/issues/533)
- Cache file location [\#491](https://github.com/qossmic/deptrac/issues/491)
- Analyze global functions [\#280](https://github.com/qossmic/deptrac/issues/280)
- Allow Transitive Dependencies [\#202](https://github.com/qossmic/deptrac/issues/202)
- Add ability to inherit from a parent layer [\#158](https://github.com/qossmic/deptrac/issues/158)
- grouping layers [\#77](https://github.com/qossmic/deptrac/issues/77)
- Add command to visualize a group [\#69](https://github.com/qossmic/deptrac/issues/69)
- Support for dot clusters [\#39](https://github.com/qossmic/deptrac/issues/39)

## [0.13.0](https://github.com/qossmic/deptrac/tree/0.13.0) (2021-04-16)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.12.0...0.13.0)

**Implemented enhancements:**

- Add debug command [\#327](https://github.com/qossmic/deptrac/issues/327)

**Closed issues:**

- update xdebug-handler to v2 [\#530](https://github.com/qossmic/deptrac/issues/530)

**Merged pull requests:**

- update xdebug-handler to v2 [\#532](https://github.com/qossmic/deptrac/pull/532) ([smoench](https://github.com/smoench))
- Bump jetbrains/phpstorm-stubs from 2020.2 to 2020.3 [\#531](https://github.com/qossmic/deptrac/pull/531) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/console from 5.2.5 to 5.2.6 [\#529](https://github.com/qossmic/deptrac/pull/529) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/dependency-injection from 5.2.5 to 5.2.6 [\#528](https://github.com/qossmic/deptrac/pull/528) ([dependabot[bot]](https://github.com/apps/dependabot))
- adds debug commands for layer and class-likes [\#527](https://github.com/qossmic/deptrac/pull/527) ([smoench](https://github.com/smoench))
- Bump composer/xdebug-handler from 1.4.5 to 1.4.6 [\#526](https://github.com/qossmic/deptrac/pull/526) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump phpstan/phpdoc-parser from 0.4.12 to 0.4.14 [\#525](https://github.com/qossmic/deptrac/pull/525) ([dependabot[bot]](https://github.com/apps/dependabot))

## [0.12.0](https://github.com/qossmic/deptrac/tree/0.12.0) (2021-03-12)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.11.1...0.12.0)

**Implemented enhancements:**

- Set document root for directory collector [\#328](https://github.com/qossmic/deptrac/issues/328)
- emit errors instead of warnings, when `--report-uncovered --fail-on-uncovered` is used in combination [\#510](https://github.com/qossmic/deptrac/issues/510)
- Paths relative to depfile [\#453](https://github.com/qossmic/deptrac/issues/453)

**Fixed bugs:**

- fix fqsen type resolver [\#519](https://github.com/qossmic/deptrac/pull/519) ([smoench](https://github.com/smoench))

**Closed issues:**

- `\` vs `\\`  in depfile [\#508](https://github.com/qossmic/deptrac/issues/508)
- using `/vendor/` as a layer [\#506](https://github.com/qossmic/deptrac/issues/506)
- Feature request: Except layer collector [\#503](https://github.com/qossmic/deptrac/issues/503)
- mixed directory separators [\#500](https://github.com/qossmic/deptrac/issues/500)
- Feature request: Parametrised imports [\#493](https://github.com/qossmic/deptrac/issues/493)
- Error Analysis psalm ? [\#349](https://github.com/qossmic/deptrac/issues/349)
- hackable/flexible depfile configuration [\#76](https://github.com/qossmic/deptrac/issues/76)
- add support for Service Configurations [\#20](https://github.com/qossmic/deptrac/issues/20)
- Use TableOutputFormatter as default [\#492](https://github.com/qossmic/deptrac/issues/492)
- How are classes handled which are part of several layers [\#488](https://github.com/qossmic/deptrac/issues/488)
- Don't report skipped violations by default [\#487](https://github.com/qossmic/deptrac/issues/487)
- Extending/ including config [\#486](https://github.com/qossmic/deptrac/issues/486)
- Remove deprecated options [\#481](https://github.com/qossmic/deptrac/issues/481)

**Merged pull requests:**

- Bump symfony/yaml from 5.2.4 to 5.2.5 [\#520](https://github.com/qossmic/deptrac/pull/520) ([dependabot[bot]](https://github.com/apps/dependabot))
- update build matrix OS [\#502](https://github.com/qossmic/deptrac/pull/502) ([smoench](https://github.com/smoench))
- Parameters [\#501](https://github.com/qossmic/deptrac/pull/501) ([smoench](https://github.com/smoench))
- \[POC\] report warnings about classes are in two or more layers [\#499](https://github.com/qossmic/deptrac/pull/499) ([smoench](https://github.com/smoench))
- Report uncovered as errors with GithubActionOutputFormatter [\#523](https://github.com/qossmic/deptrac/pull/523) ([smoench](https://github.com/smoench))
- Bump symfony/console from 5.2.4 to 5.2.5 [\#522](https://github.com/qossmic/deptrac/pull/522) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/dependency-injection from 5.2.4 to 5.2.5 [\#521](https://github.com/qossmic/deptrac/pull/521) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/options-resolver from 5.2.3 to 5.2.4 [\#517](https://github.com/qossmic/deptrac/pull/517) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/dependency-injection from 5.2.3 to 5.2.4 [\#516](https://github.com/qossmic/deptrac/pull/516) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/finder from 5.2.3 to 5.2.4 [\#515](https://github.com/qossmic/deptrac/pull/515) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/config from 5.2.3 to 5.2.4 [\#514](https://github.com/qossmic/deptrac/pull/514) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/yaml from 5.2.3 to 5.2.4 [\#513](https://github.com/qossmic/deptrac/pull/513) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/console from 5.2.3 to 5.2.4 [\#512](https://github.com/qossmic/deptrac/pull/512) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/event-dispatcher from 5.2.3 to 5.2.4 [\#511](https://github.com/qossmic/deptrac/pull/511) ([dependabot[bot]](https://github.com/apps/dependabot))
- readme: fix `classNameRegex` example [\#507](https://github.com/qossmic/deptrac/pull/507) ([clxmstaab](https://github.com/clxmstaab))
- Bump phpstan/phpdoc-parser from 0.4.11 to 0.4.12 [\#505](https://github.com/qossmic/deptrac/pull/505) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump phpstan/phpdoc-parser from 0.4.10 to 0.4.11 [\#498](https://github.com/qossmic/deptrac/pull/498) ([dependabot[bot]](https://github.com/apps/dependabot))
- Remove deprecated formatter options [\#497](https://github.com/qossmic/deptrac/pull/497) ([smoench](https://github.com/smoench))
- Use TableOutputFormatter as default [\#496](https://github.com/qossmic/deptrac/pull/496) ([smoench](https://github.com/smoench))
- Remove deprecated options [\#495](https://github.com/qossmic/deptrac/pull/495) ([smoench](https://github.com/smoench))
- Don't report skipped violations by default [\#494](https://github.com/qossmic/deptrac/pull/494) ([smoench](https://github.com/smoench))
- update tools [\#490](https://github.com/qossmic/deptrac/pull/490) ([smoench](https://github.com/smoench))
- \[RFC\] Import configuration files [\#489](https://github.com/qossmic/deptrac/pull/489) ([smoench](https://github.com/smoench))

## [0.11.1](https://github.com/qossmic/deptrac/tree/0.11.1) (2021-02-09)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.11.0...0.11.1)

**Closed issues:**

- coverage 100% [\#16](https://github.com/qossmic/deptrac/issues/16)
- Add support for report skipped option in GithubActionsOutputFormatter [\#483](https://github.com/qossmic/deptrac/issues/483)
- Add support for report skipped option in TableOutputFormatter [\#482](https://github.com/qossmic/deptrac/issues/482)

**Merged pull requests:**

- Add support for report skipped option in TableOutputFormatter [\#485](https://github.com/qossmic/deptrac/pull/485) ([smoench](https://github.com/smoench))
- Add support for report skipped option in GithubActionsOutputFormatter [\#484](https://github.com/qossmic/deptrac/pull/484) ([sasezaki](https://github.com/sasezaki))
- Bump symfony/yaml from 5.2.2 to 5.2.3 [\#480](https://github.com/qossmic/deptrac/pull/480) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/finder from 5.2.2 to 5.2.3 [\#479](https://github.com/qossmic/deptrac/pull/479) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/event-dispatcher from 5.2.2 to 5.2.3 [\#478](https://github.com/qossmic/deptrac/pull/478) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/dependency-injection from 5.2.2 to 5.2.3 [\#477](https://github.com/qossmic/deptrac/pull/477) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/console from 5.2.2 to 5.2.3 [\#476](https://github.com/qossmic/deptrac/pull/476) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/config from 5.2.2 to 5.2.3 [\#475](https://github.com/qossmic/deptrac/pull/475) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/options-resolver from 5.2.2 to 5.2.3 [\#474](https://github.com/qossmic/deptrac/pull/474) ([dependabot[bot]](https://github.com/apps/dependabot))
- Add option to not print skipped violations in ConsoleOutputFormatter [\#471](https://github.com/qossmic/deptrac/pull/471) ([lprzybylek](https://github.com/lprzybylek))

## [0.11.0](https://github.com/qossmic/deptrac/tree/0.11.0) (2021-02-01)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.10.3...0.11.0)

**Merged pull requests:**

- Readme: added missing collectors into outline [\#472](https://github.com/qossmic/deptrac/pull/472) ([staabm](https://github.com/staabm))
- Welcome to QOSSMIC 🚀 [\#470](https://github.com/qossmic/deptrac/pull/470) ([smoench](https://github.com/smoench))

## [0.10.3](https://github.com/qossmic/deptrac/tree/0.10.3) (2021-01-29)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.10.2...0.10.3)

**Implemented enhancements:**

- Support non annotated property type [\#456](https://github.com/qossmic/deptrac/pull/456) ([sasezaki](https://github.com/sasezaki))

**Closed issues:**

- can not find declaration property type dependency. [\#455](https://github.com/qossmic/deptrac/issues/455)
- must not depend on Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository \(Repository on Service\) [\#448](https://github.com/qossmic/deptrac/issues/448)
- Baseline dump documentation incomplete [\#414](https://github.com/qossmic/deptrac/issues/414)
- False positive: How can this be a violation? [\#351](https://github.com/qossmic/deptrac/issues/351)
- Add support for sarb.json output format to baselines can be made [\#268](https://github.com/qossmic/deptrac/issues/268)

**Merged pull requests:**

-  use psalm level 1 [\#330](https://github.com/qossmic/deptrac/pull/330) ([smoench](https://github.com/smoench))
- clean up the temp file even when the test fails [\#469](https://github.com/qossmic/deptrac/pull/469) ([xabbuh](https://github.com/xabbuh))
- replace useless specific output file to temp file at test. [\#468](https://github.com/qossmic/deptrac/pull/468) ([sasezaki](https://github.com/sasezaki))
- update psalm to v4.4.1 [\#467](https://github.com/qossmic/deptrac/pull/467) ([smoench](https://github.com/smoench))
- update phpstan to v0.12.70 [\#466](https://github.com/qossmic/deptrac/pull/466) ([smoench](https://github.com/smoench))
- Readme: fix typo [\#465](https://github.com/qossmic/deptrac/pull/465) ([staabm](https://github.com/staabm))
- update php-cs-fixer to v2.18.2 [\#464](https://github.com/qossmic/deptrac/pull/464) ([smoench](https://github.com/smoench))
- Bump symfony/config from 5.2.1 to 5.2.2 [\#463](https://github.com/qossmic/deptrac/pull/463) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/options-resolver from 5.2.1 to 5.2.2 [\#462](https://github.com/qossmic/deptrac/pull/462) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/dependency-injection from 5.2.1 to 5.2.2 [\#461](https://github.com/qossmic/deptrac/pull/461) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/finder from 5.2.1 to 5.2.2 [\#460](https://github.com/qossmic/deptrac/pull/460) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/console from 5.2.1 to 5.2.2 [\#459](https://github.com/qossmic/deptrac/pull/459) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/yaml from 5.2.1 to 5.2.2 [\#458](https://github.com/qossmic/deptrac/pull/458) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/event-dispatcher from 5.2.1 to 5.2.2 [\#457](https://github.com/qossmic/deptrac/pull/457) ([dependabot[bot]](https://github.com/apps/dependabot))
- simplify cache file reading [\#452](https://github.com/qossmic/deptrac/pull/452) ([smoench](https://github.com/smoench))
- simplify configuration loading [\#451](https://github.com/qossmic/deptrac/pull/451) ([smoench](https://github.com/smoench))
- simplify file parsing [\#450](https://github.com/qossmic/deptrac/pull/450) ([smoench](https://github.com/smoench))
- add infection testing [\#447](https://github.com/qossmic/deptrac/pull/447) ([smoench](https://github.com/smoench))
- update psalm to version 4.3.1 [\#446](https://github.com/qossmic/deptrac/pull/446) ([smoench](https://github.com/smoench))
- update phpunit to version 8.5.13 [\#445](https://github.com/qossmic/deptrac/pull/445) ([smoench](https://github.com/smoench))
- update phpstan to version 0.12.64 [\#444](https://github.com/qossmic/deptrac/pull/444) ([smoench](https://github.com/smoench))
- update php-cs-fixer to version 2.17.2 [\#443](https://github.com/qossmic/deptrac/pull/443) ([smoench](https://github.com/smoench))
- update box to version 3.11.0 [\#442](https://github.com/qossmic/deptrac/pull/442) ([smoench](https://github.com/smoench))
- Bump symfony/dependency-injection from 5.2.0 to 5.2.1 [\#441](https://github.com/qossmic/deptrac/pull/441) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/config from 5.2.0 to 5.2.1 [\#440](https://github.com/qossmic/deptrac/pull/440) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/console from 5.2.0 to 5.2.1 [\#439](https://github.com/qossmic/deptrac/pull/439) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/yaml from 5.2.0 to 5.2.1 [\#438](https://github.com/qossmic/deptrac/pull/438) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump nikic/php-parser from 4.10.3 to 4.10.4 [\#437](https://github.com/qossmic/deptrac/pull/437) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/finder from 5.2.0 to 5.2.1 [\#436](https://github.com/qossmic/deptrac/pull/436) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/event-dispatcher from 5.2.0 to 5.2.1 [\#435](https://github.com/qossmic/deptrac/pull/435) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/options-resolver from 5.2.0 to 5.2.1 [\#434](https://github.com/qossmic/deptrac/pull/434) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump phpstan/phpdoc-parser from 0.4.9 to 0.4.10 [\#433](https://github.com/qossmic/deptrac/pull/433) ([dependabot[bot]](https://github.com/apps/dependabot))

## [0.10.2](https://github.com/qossmic/deptrac/tree/0.10.2) (2020-12-08)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.10.1...0.10.2)

**Fixed bugs:**

- Using deptrac 0.10.1 with php 7.4 results in error [\#431](https://github.com/qossmic/deptrac/issues/431)

**Merged pull requests:**

- update phpstan to 0.12.58 [\#430](https://github.com/qossmic/deptrac/pull/430) ([smoench](https://github.com/smoench))
- don't scope symfony polyfill's [\#429](https://github.com/qossmic/deptrac/pull/429) ([smoench](https://github.com/smoench))
- Bump symfony/dependency-injection from 5.1.8 to 5.2.0 [\#427](https://github.com/qossmic/deptrac/pull/427) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/console from 5.1.8 to 5.2.0 [\#426](https://github.com/qossmic/deptrac/pull/426) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/config from 5.1.8 to 5.2.0 [\#425](https://github.com/qossmic/deptrac/pull/425) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/options-resolver from 5.1.8 to 5.2.0 [\#424](https://github.com/qossmic/deptrac/pull/424) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/event-dispatcher from 5.1.8 to 5.2.0 [\#423](https://github.com/qossmic/deptrac/pull/423) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/finder from 5.1.8 to 5.2.0 [\#422](https://github.com/qossmic/deptrac/pull/422) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/yaml from 5.1.8 to 5.2.0 [\#421](https://github.com/qossmic/deptrac/pull/421) ([dependabot[bot]](https://github.com/apps/dependabot))

## [0.10.1](https://github.com/qossmic/deptrac/tree/0.10.1) (2020-12-04)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.10.0...0.10.1)

**Merged pull requests:**

- Bump nikic/php-parser from 4.10.2 to 4.10.3 [\#428](https://github.com/qossmic/deptrac/pull/428) ([dependabot[bot]](https://github.com/apps/dependabot))
- update to box v3.9.1 [\#420](https://github.com/qossmic/deptrac/pull/420) ([smoench](https://github.com/smoench))
- update jetbrains/phpstorm-stubs to v2020.2 [\#419](https://github.com/qossmic/deptrac/pull/419) ([smoench](https://github.com/smoench))
- add psalm pseudo-types [\#418](https://github.com/qossmic/deptrac/pull/418) ([marcosh](https://github.com/marcosh))

## [0.10.0](https://github.com/qossmic/deptrac/tree/0.10.0) (2020-11-20)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.9.0...0.10.0)

**Fixed bugs:**

- Deptrac fails on invalid PHP8 code [\#416](https://github.com/qossmic/deptrac/issues/416)

**Closed issues:**

- 0.8.2 -\> 0.9.0 2x performance degradation in CI [\#413](https://github.com/qossmic/deptrac/issues/413)
- PHP 8 support [\#412](https://github.com/qossmic/deptrac/issues/412)

**Merged pull requests:**

- Allow PHP 8 [\#417](https://github.com/qossmic/deptrac/pull/417) ([smoench](https://github.com/smoench))
- Bump composer/xdebug-handler from 1.4.4 to 1.4.5 [\#415](https://github.com/qossmic/deptrac/pull/415) ([dependabot[bot]](https://github.com/apps/dependabot))

## [0.9.0](https://github.com/qossmic/deptrac/tree/0.9.0) (2020-10-30)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.8.2...0.9.0)

**Implemented enhancements:**

- \[GithubActionsOutputFormatter\] print inherit path [\#376](https://github.com/qossmic/deptrac/issues/376)
- Optionally hide the skipped violations and warn that skipped violation is already non-existent [\#352](https://github.com/qossmic/deptrac/issues/352)
- Load/instantiate collectors by FQCN [\#164](https://github.com/qossmic/deptrac/issues/164)
- Enhancement: Mark test classes as final [\#400](https://github.com/qossmic/deptrac/pull/400) ([localheinz](https://github.com/localheinz))
- Enhancement: Enable final\_static\_access fixer [\#397](https://github.com/qossmic/deptrac/pull/397) ([localheinz](https://github.com/localheinz))
- Simplify formatter options [\#396](https://github.com/qossmic/deptrac/pull/396) ([smoench](https://github.com/smoench))
- \[GraphViz\] display depend on count [\#378](https://github.com/qossmic/deptrac/pull/378) ([smoench](https://github.com/smoench))
- Fix PHPUnit phar extension name, so PHPStorm can analyse it and refrences can be used in tests [\#353](https://github.com/qossmic/deptrac/pull/353) ([smoench](https://github.com/smoench))
- introduce table output formatter [\#337](https://github.com/qossmic/deptrac/pull/337) ([smoench](https://github.com/smoench))
- Load Collectors by FQCN [\#320](https://github.com/qossmic/deptrac/pull/320) ([DanielBadura](https://github.com/DanielBadura))

**Closed issues:**

- Improve console formatter output [\#326](https://github.com/qossmic/deptrac/issues/326)
- allow custom collectors [\#19](https://github.com/qossmic/deptrac/issues/19)

**Merged pull requests:**

- Bump symfony/options-resolver from 5.1.7 to 5.1.8 [\#411](https://github.com/qossmic/deptrac/pull/411) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/finder from 5.1.7 to 5.1.8 [\#410](https://github.com/qossmic/deptrac/pull/410) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/config from 5.1.7 to 5.1.8 [\#409](https://github.com/qossmic/deptrac/pull/409) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/event-dispatcher from 5.1.7 to 5.1.8 [\#408](https://github.com/qossmic/deptrac/pull/408) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/dependency-injection from 5.1.7 to 5.1.8 [\#407](https://github.com/qossmic/deptrac/pull/407) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/console from 5.1.7 to 5.1.8 [\#406](https://github.com/qossmic/deptrac/pull/406) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/yaml from 5.1.7 to 5.1.8 [\#405](https://github.com/qossmic/deptrac/pull/405) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump composer/xdebug-handler from 1.4.3 to 1.4.4 [\#404](https://github.com/qossmic/deptrac/pull/404) ([dependabot[bot]](https://github.com/apps/dependabot))
- report unmatched skipped violations [\#403](https://github.com/qossmic/deptrac/pull/403) ([smoench](https://github.com/smoench))
- github actions show inherit path [\#402](https://github.com/qossmic/deptrac/pull/402) ([smoench](https://github.com/smoench))
- use composer v2 for CI [\#401](https://github.com/qossmic/deptrac/pull/401) ([smoench](https://github.com/smoench))
- Fix: Reject rule sets referencing unknown layers [\#399](https://github.com/qossmic/deptrac/pull/399) ([localheinz](https://github.com/localheinz))
- Fix: Reject duplicate layer names [\#398](https://github.com/qossmic/deptrac/pull/398) ([localheinz](https://github.com/localheinz))
- Introduce BaselineOutputFormatter [\#395](https://github.com/qossmic/deptrac/pull/395) ([marcelthole](https://github.com/marcelthole))
- Bump symfony/dependency-injection from 5.1.6 to 5.1.7 [\#394](https://github.com/qossmic/deptrac/pull/394) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/options-resolver from 5.1.6 to 5.1.7 [\#393](https://github.com/qossmic/deptrac/pull/393) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/finder from 5.1.6 to 5.1.7 [\#392](https://github.com/qossmic/deptrac/pull/392) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/event-dispatcher from 5.1.6 to 5.1.7 [\#391](https://github.com/qossmic/deptrac/pull/391) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/yaml from 5.1.6 to 5.1.7 [\#390](https://github.com/qossmic/deptrac/pull/390) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/console from 5.1.6 to 5.1.7 [\#389](https://github.com/qossmic/deptrac/pull/389) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/config from 5.1.6 to 5.1.7 [\#388](https://github.com/qossmic/deptrac/pull/388) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/console from 5.1.5 to 5.1.6 [\#387](https://github.com/qossmic/deptrac/pull/387) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/yaml from 5.1.5 to 5.1.6 [\#386](https://github.com/qossmic/deptrac/pull/386) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump nikic/php-parser from 4.10.1 to 4.10.2 [\#385](https://github.com/qossmic/deptrac/pull/385) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/finder from 5.1.5 to 5.1.6 [\#384](https://github.com/qossmic/deptrac/pull/384) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/config from 5.1.5 to 5.1.6 [\#383](https://github.com/qossmic/deptrac/pull/383) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/event-dispatcher from 5.1.5 to 5.1.6 [\#382](https://github.com/qossmic/deptrac/pull/382) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/dependency-injection from 5.1.5 to 5.1.6 [\#381](https://github.com/qossmic/deptrac/pull/381) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/options-resolver from 5.1.5 to 5.1.6 [\#380](https://github.com/qossmic/deptrac/pull/380) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump nikic/php-parser from 4.10.0 to 4.10.1 [\#379](https://github.com/qossmic/deptrac/pull/379) ([dependabot[bot]](https://github.com/apps/dependabot))
- update tools [\#377](https://github.com/qossmic/deptrac/pull/377) ([smoench](https://github.com/smoench))
- Bump nikic/php-parser from 4.9.1 to 4.10.0 [\#374](https://github.com/qossmic/deptrac/pull/374) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump phpdocumentor/type-resolver from 1.3.0 to 1.4.0 [\#372](https://github.com/qossmic/deptrac/pull/372) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/event-dispatcher from 5.1.4 to 5.1.5 [\#371](https://github.com/qossmic/deptrac/pull/371) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/options-resolver from 5.1.4 to 5.1.5 [\#370](https://github.com/qossmic/deptrac/pull/370) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/dependency-injection from 5.1.4 to 5.1.5 [\#369](https://github.com/qossmic/deptrac/pull/369) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/finder from 5.1.4 to 5.1.5 [\#368](https://github.com/qossmic/deptrac/pull/368) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/config from 5.1.4 to 5.1.5 [\#367](https://github.com/qossmic/deptrac/pull/367) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/console from 5.1.4 to 5.1.5 [\#366](https://github.com/qossmic/deptrac/pull/366) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/yaml from 5.1.4 to 5.1.5 [\#365](https://github.com/qossmic/deptrac/pull/365) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/config from 5.1.3 to 5.1.4 [\#364](https://github.com/qossmic/deptrac/pull/364) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/event-dispatcher from 5.1.3 to 5.1.4 [\#363](https://github.com/qossmic/deptrac/pull/363) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/dependency-injection from 5.1.3 to 5.1.4 [\#362](https://github.com/qossmic/deptrac/pull/362) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/yaml from 5.1.3 to 5.1.4 [\#361](https://github.com/qossmic/deptrac/pull/361) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/finder from 5.1.3 to 5.1.4 [\#360](https://github.com/qossmic/deptrac/pull/360) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/options-resolver from 5.1.3 to 5.1.4 [\#359](https://github.com/qossmic/deptrac/pull/359) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/console from 5.1.3 to 5.1.4 [\#358](https://github.com/qossmic/deptrac/pull/358) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump nikic/php-parser from 4.9.0 to 4.9.1 [\#357](https://github.com/qossmic/deptrac/pull/357) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump composer/xdebug-handler from 1.4.2 to 1.4.3 [\#356](https://github.com/qossmic/deptrac/pull/356) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump nikic/php-parser from 4.8.0 to 4.9.0 [\#355](https://github.com/qossmic/deptrac/pull/355) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump nikic/php-parser from 4.7.0 to 4.8.0 [\#354](https://github.com/qossmic/deptrac/pull/354) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump phpstan/phpdoc-parser from 0.4.8 to 0.4.9 [\#350](https://github.com/qossmic/deptrac/pull/350) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump nikic/php-parser from 4.6.0 to 4.7.0 [\#348](https://github.com/qossmic/deptrac/pull/348) ([dependabot[bot]](https://github.com/apps/dependabot))

## [0.8.2](https://github.com/qossmic/deptrac/tree/0.8.2) (2020-07-24)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.8.1...0.8.2)

**Implemented enhancements:**

- Default file from depfile.yml to depfile.yaml [\#332](https://github.com/qossmic/deptrac/issues/332)
- add option to report uncovered dependencies for GitHubActionFormatter [\#325](https://github.com/qossmic/deptrac/issues/325)
- Some Psalm annotations/types break the scan [\#329](https://github.com/qossmic/deptrac/issues/329)

**Fixed bugs:**

- unknown collector type "extends" [\#333](https://github.com/qossmic/deptrac/issues/333)

**Merged pull requests:**

- Bump symfony/finder from 5.1.2 to 5.1.3 [\#347](https://github.com/qossmic/deptrac/pull/347) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/console from 5.1.2 to 5.1.3 [\#346](https://github.com/qossmic/deptrac/pull/346) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/yaml from 5.1.2 to 5.1.3 [\#345](https://github.com/qossmic/deptrac/pull/345) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/dependency-injection from 5.1.2 to 5.1.3 [\#344](https://github.com/qossmic/deptrac/pull/344) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/config from 5.1.2 to 5.1.3 [\#343](https://github.com/qossmic/deptrac/pull/343) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump nikic/php-parser from 4.5.0 to 4.6.0 [\#342](https://github.com/qossmic/deptrac/pull/342) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump phpdocumentor/type-resolver from 1.1.0 to 1.3.0 [\#341](https://github.com/qossmic/deptrac/pull/341) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/options-resolver from 5.1.2 to 5.1.3 [\#340](https://github.com/qossmic/deptrac/pull/340) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/event-dispatcher from 5.1.2 to 5.1.3 [\#339](https://github.com/qossmic/deptrac/pull/339) ([dependabot[bot]](https://github.com/apps/dependabot))
- Add option to report uncovered dependencies for GitHubActionFormatter [\#338](https://github.com/qossmic/deptrac/pull/338) ([jschaedl](https://github.com/jschaedl))
- ignore \(psalm\) pseudo types [\#336](https://github.com/qossmic/deptrac/pull/336) ([smoench](https://github.com/smoench))
- Add missing Collectors into the container [\#335](https://github.com/qossmic/deptrac/pull/335) ([DanielBadura](https://github.com/DanielBadura))
- Change default file to depfile.yaml [\#334](https://github.com/qossmic/deptrac/pull/334) ([DanielBadura](https://github.com/DanielBadura))

## [0.8.1](https://github.com/qossmic/deptrac/tree/0.8.1) (2020-07-10)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.8.0...0.8.1)

**Implemented enhancements:**

- Enable GitHub Actions formatter automatically [\#318](https://github.com/qossmic/deptrac/issues/318)

**Fixed bugs:**

- 'Report uncovered' does not work properly with PHP built-in function/classes if they are imported [\#319](https://github.com/qossmic/deptrac/issues/319)

**Merged pull requests:**

- install tools with phive [\#316](https://github.com/qossmic/deptrac/pull/316) ([smoench](https://github.com/smoench))
- upgrade to symfony 5.1 [\#315](https://github.com/qossmic/deptrac/pull/315) ([smoench](https://github.com/smoench))
- adds jetbrains/phpstorm-stubs for not blaming about uncovered internal classes [\#314](https://github.com/qossmic/deptrac/pull/314) ([smoench](https://github.com/smoench))
- Adds uses, extends and inherits collectors. [\#311](https://github.com/qossmic/deptrac/pull/311) ([dbrumann](https://github.com/dbrumann))
- Fixes key for implements Collector [\#308](https://github.com/qossmic/deptrac/pull/308) ([dbrumann](https://github.com/dbrumann))
- Add flag --fail-on-uncovered \(closes \#306\) [\#307](https://github.com/qossmic/deptrac/pull/307) ([hugochinchilla](https://github.com/hugochinchilla))
- Add GitHub Actions Output Formatter [\#305](https://github.com/qossmic/deptrac/pull/305) ([jtaylor100](https://github.com/jtaylor100))
- update github actions [\#303](https://github.com/qossmic/deptrac/pull/303) ([smoench](https://github.com/smoench))
- Enable GithubActionsOutputFormatter by default in GithubActions environment [\#324](https://github.com/qossmic/deptrac/pull/324) ([jschaedl](https://github.com/jschaedl))
- psalm level 2 [\#323](https://github.com/qossmic/deptrac/pull/323) ([smoench](https://github.com/smoench))
- differentiate between possible use types [\#322](https://github.com/qossmic/deptrac/pull/322) ([smoench](https://github.com/smoench))
- Add Psalm [\#321](https://github.com/qossmic/deptrac/pull/321) ([DanielBadura](https://github.com/DanielBadura))

## [0.8.0](https://github.com/qossmic/deptrac/tree/0.8.0) (2020-06-19)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.7.1...0.8.0)

**Implemented enhancements:**

- GitHub Action Output formatter [\#282](https://github.com/qossmic/deptrac/issues/282)

**Closed issues:**

- Check why the Symfony 5.1 upgrade increased the phar file size from ~800kb to ~2.1mb [\#317](https://github.com/qossmic/deptrac/issues/317)
- Integrate jetbrains/phpstorm-stubs [\#313](https://github.com/qossmic/deptrac/issues/313)
- Add `extends` and `traitUse` collectors [\#309](https://github.com/qossmic/deptrac/issues/309)
- Provide flag to fail if uncovered dependencies found [\#306](https://github.com/qossmic/deptrac/issues/306)
- PHPStan Not Part of the Normal Build [\#304](https://github.com/qossmic/deptrac/issues/304)
- Add file names in the output to integrate with Git hooks [\#291](https://github.com/qossmic/deptrac/issues/291)

## [0.7.1](https://github.com/qossmic/deptrac/tree/0.7.1) (2020-05-04)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.7.0...0.7.1)

**Fixed bugs:**

- Scan breaks on certain docblocks in 0.7.0 [\#301](https://github.com/qossmic/deptrac/issues/301)

**Merged pull requests:**

- Bugfix: cover more DocBlock types [\#302](https://github.com/qossmic/deptrac/pull/302) ([smoench](https://github.com/smoench))

## [0.7.0](https://github.com/qossmic/deptrac/tree/0.7.0) (2020-05-02)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.6.0...0.7.0)

**Implemented enhancements:**

- Disable Xdebug [\#293](https://github.com/qossmic/deptrac/issues/293)
- Make deptrac complain about namespaces that don't belong to any layers  [\#231](https://github.com/qossmic/deptrac/issues/231)
- Empty jUnit report when all checks passed [\#205](https://github.com/qossmic/deptrac/issues/205)

**Fixed bugs:**

- check for missing statements / dependencies [\#212](https://github.com/qossmic/deptrac/issues/212)

**Closed issues:**

- Add separate cache file for each depfile [\#299](https://github.com/qossmic/deptrac/issues/299)
- Allow installation as `composer` dev-dependency [\#277](https://github.com/qossmic/deptrac/issues/277)
- docs for MethodNameCollector [\#49](https://github.com/qossmic/deptrac/issues/49)

**Merged pull requests:**

- adds file reference builder [\#300](https://github.com/qossmic/deptrac/pull/300) ([smoench](https://github.com/smoench))
- adds implements collector [\#298](https://github.com/qossmic/deptrac/pull/298) ([smoench](https://github.com/smoench))
- Add doc for MethodCollector and misc [\#297](https://github.com/qossmic/deptrac/pull/297) ([smoench](https://github.com/smoench))
- use php config files [\#296](https://github.com/qossmic/deptrac/pull/296) ([smoench](https://github.com/smoench))
- upload phar artifact [\#295](https://github.com/qossmic/deptrac/pull/295) ([smoench](https://github.com/smoench))
- adds composer/xdebug-handler [\#294](https://github.com/qossmic/deptrac/pull/294) ([smoench](https://github.com/smoench))
- dependency updates [\#292](https://github.com/qossmic/deptrac/pull/292) ([smoench](https://github.com/smoench))
- jUnit: report successful + uncovered testcases [\#290](https://github.com/qossmic/deptrac/pull/290) ([smoench](https://github.com/smoench))
- dependency updates [\#289](https://github.com/qossmic/deptrac/pull/289) ([smoench](https://github.com/smoench))
- remove banner [\#288](https://github.com/qossmic/deptrac/pull/288) ([smoench](https://github.com/smoench))
- refactor type resolving [\#287](https://github.com/qossmic/deptrac/pull/287) ([smoench](https://github.com/smoench))
- use latest phpstan version [\#286](https://github.com/qossmic/deptrac/pull/286) ([smoench](https://github.com/smoench))
- report uncovered dependencies [\#285](https://github.com/qossmic/deptrac/pull/285) ([smoench](https://github.com/smoench))
- update dependencies [\#284](https://github.com/qossmic/deptrac/pull/284) ([smoench](https://github.com/smoench))
- improve analysing performance [\#283](https://github.com/qossmic/deptrac/pull/283) ([smoench](https://github.com/smoench))
- update actions config [\#281](https://github.com/qossmic/deptrac/pull/281) ([smoench](https://github.com/smoench))
- resolve file occurrences of dependencies [\#279](https://github.com/qossmic/deptrac/pull/279) ([smoench](https://github.com/smoench))
- make AstMap immutable [\#278](https://github.com/qossmic/deptrac/pull/278) ([smoench](https://github.com/smoench))
- upgrade to symfony 5.0 [\#276](https://github.com/qossmic/deptrac/pull/276) ([smoench](https://github.com/smoench))
- use checkout fetch depth one [\#275](https://github.com/qossmic/deptrac/pull/275) ([smoench](https://github.com/smoench))
- use github actions [\#274](https://github.com/qossmic/deptrac/pull/274) ([smoench](https://github.com/smoench))
- Update README.md [\#272](https://github.com/qossmic/deptrac/pull/272) ([radimvaculik](https://github.com/radimvaculik))
- test on PHP 7.4 [\#271](https://github.com/qossmic/deptrac/pull/271) ([smoench](https://github.com/smoench))
- add xml formatter [\#270](https://github.com/qossmic/deptrac/pull/270) ([timglabisch](https://github.com/timglabisch))
- POC: track uncovered dependencies [\#266](https://github.com/qossmic/deptrac/pull/266) ([smoench](https://github.com/smoench))

## [0.6.0](https://github.com/qossmic/deptrac/tree/0.6.0) (2019-10-18)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.5.0...0.6.0)

**Closed issues:**

- README should state that this applies only to PHP [\#260](https://github.com/qossmic/deptrac/issues/260)
- Resolving docblock annotations [\#240](https://github.com/qossmic/deptrac/issues/240)
- ::class construct not resolved as dependency [\#239](https://github.com/qossmic/deptrac/issues/239)
- Anonymous class extending class from different layer not resolved as dependency [\#238](https://github.com/qossmic/deptrac/issues/238)
- bool collector not working as documented [\#237](https://github.com/qossmic/deptrac/issues/237)
- Deprecated Graphviz windows download link in README.md [\#222](https://github.com/qossmic/deptrac/issues/222)
- idea, replace nikic/php-parser with syntect [\#181](https://github.com/qossmic/deptrac/issues/181)
- Sign the phar file [\#112](https://github.com/qossmic/deptrac/issues/112)
- add support for routing [\#21](https://github.com/qossmic/deptrac/issues/21)

**Merged pull requests:**

- improve tests [\#267](https://github.com/qossmic/deptrac/pull/267) ([smoench](https://github.com/smoench))
- Bugfix: classes in other namespaces are resolved in same namespace [\#265](https://github.com/qossmic/deptrac/pull/265) ([smoench](https://github.com/smoench))
- simplify console output mode \(verbose\) [\#264](https://github.com/qossmic/deptrac/pull/264) ([smoench](https://github.com/smoench))
- phpstan - inferPrivatePropertyTypeFromConstructor [\#263](https://github.com/qossmic/deptrac/pull/263) ([smoench](https://github.com/smoench))
- dependency updates [\#262](https://github.com/qossmic/deptrac/pull/262) ([smoench](https://github.com/smoench))
- Update README.md [\#261](https://github.com/qossmic/deptrac/pull/261) ([dbrumann](https://github.com/dbrumann))
- phpstan level max [\#259](https://github.com/qossmic/deptrac/pull/259) ([smoench](https://github.com/smoench))
- Enhancement: Throw exception when configuration can be parsed as yaml, but does not contain array [\#258](https://github.com/qossmic/deptrac/pull/258) ([localheinz](https://github.com/localheinz))
- adds missing dependency resolver test [\#257](https://github.com/qossmic/deptrac/pull/257) ([smoench](https://github.com/smoench))
- Enhancement: Throw exception when configuration cannot be parsed as yaml [\#256](https://github.com/qossmic/deptrac/pull/256) ([localheinz](https://github.com/localheinz))
- Enhancement: Add return type declarations to closures [\#255](https://github.com/qossmic/deptrac/pull/255) ([localheinz](https://github.com/localheinz))
- Enhancement: Enable static\_lambda fixer [\#254](https://github.com/qossmic/deptrac/pull/254) ([localheinz](https://github.com/localheinz))
- Enhancement: Keep rules sorted in .php\_cs.dist [\#253](https://github.com/qossmic/deptrac/pull/253) ([localheinz](https://github.com/localheinz))
- Enhancement: Introduce temporary variable [\#252](https://github.com/qossmic/deptrac/pull/252) ([localheinz](https://github.com/localheinz))
- Enhancement: Update phpstan/phpstan [\#251](https://github.com/qossmic/deptrac/pull/251) ([localheinz](https://github.com/localheinz))
- use event classes for emitted and flattened dependencies [\#250](https://github.com/qossmic/deptrac/pull/250) ([smoench](https://github.com/smoench))
- refactor dependencies resolution [\#249](https://github.com/qossmic/deptrac/pull/249) ([smoench](https://github.com/smoench))
- Update documentation on bool collector to describe actual behaviour [\#248](https://github.com/qossmic/deptrac/pull/248) ([rpkamp](https://github.com/rpkamp))
- refactor inherits resolving [\#247](https://github.com/qossmic/deptrac/pull/247) ([smoench](https://github.com/smoench))
- naming + improvements [\#246](https://github.com/qossmic/deptrac/pull/246) ([smoench](https://github.com/smoench))
- refactor/improve method collector [\#245](https://github.com/qossmic/deptrac/pull/245) ([smoench](https://github.com/smoench))
- dependency updates [\#244](https://github.com/qossmic/deptrac/pull/244) ([smoench](https://github.com/smoench))
- anonymous class resolver [\#243](https://github.com/qossmic/deptrac/pull/243) ([smoench](https://github.com/smoench))
- upgrade box to v3.8 [\#242](https://github.com/qossmic/deptrac/pull/242) ([smoench](https://github.com/smoench))
- class constant resolver [\#241](https://github.com/qossmic/deptrac/pull/241) ([smoench](https://github.com/smoench))
- split progressbar to its own subscriber [\#236](https://github.com/qossmic/deptrac/pull/236) ([smoench](https://github.com/smoench))
- Improve console output of analyze command. [\#235](https://github.com/qossmic/deptrac/pull/235) ([temp](https://github.com/temp))
- upgrade to symfony 4.3 [\#234](https://github.com/qossmic/deptrac/pull/234) ([smoench](https://github.com/smoench))
- upgrade to phpunit 8 [\#233](https://github.com/qossmic/deptrac/pull/233) ([smoench](https://github.com/smoench))
- increase minimum php version to 7.2 [\#232](https://github.com/qossmic/deptrac/pull/232) ([smoench](https://github.com/smoench))
- Fix alignment [\#230](https://github.com/qossmic/deptrac/pull/230) ([BackEndTea](https://github.com/BackEndTea))
- improve file exclusion [\#228](https://github.com/qossmic/deptrac/pull/228) ([smoench](https://github.com/smoench))
- Fix: Remove non-applicable exclude configuration [\#227](https://github.com/qossmic/deptrac/pull/227) ([localheinz](https://github.com/localheinz))
- annotation dependency resolver [\#224](https://github.com/qossmic/deptrac/pull/224) ([smoench](https://github.com/smoench))
- Fix outdated graphviz download link in README [\#223](https://github.com/qossmic/deptrac/pull/223) ([LeoVie](https://github.com/LeoVie))

## [0.5.0](https://github.com/qossmic/deptrac/tree/0.5.0) (2019-03-15)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.4.0...0.5.0)

**Implemented enhancements:**

- Configurable cache file path [\#207](https://github.com/qossmic/deptrac/issues/207)
- Prototype: using php-parser NodeVisitor insteadof custom analysing methods [\#211](https://github.com/qossmic/deptrac/pull/211) ([smoench](https://github.com/smoench))

**Fixed bugs:**

- don't apply dependencies from prev classes to current class when file… [\#210](https://github.com/qossmic/deptrac/pull/210) ([smoench](https://github.com/smoench))

**Closed issues:**

- Installation: Why we --force-accept-unsigned [\#220](https://github.com/qossmic/deptrac/issues/220)
- Slow. Cache not working? [\#208](https://github.com/qossmic/deptrac/issues/208)

**Merged pull requests:**

- Secure installation via phive [\#221](https://github.com/qossmic/deptrac/pull/221) ([amenk](https://github.com/amenk))
- added input parameter option for cache file [\#219](https://github.com/qossmic/deptrac/pull/219) ([smoench](https://github.com/smoench))
- dependency updates [\#217](https://github.com/qossmic/deptrac/pull/217) ([smoench](https://github.com/smoench))
- Fix: Remove sudo configuration [\#216](https://github.com/qossmic/deptrac/pull/216) ([localheinz](https://github.com/localheinz))
- Enhancement: Apply @PHPUnit60Migration:risky ruleset [\#215](https://github.com/qossmic/deptrac/pull/215) ([localheinz](https://github.com/localheinz))
- Enhancement: Update phpstan/phpstan [\#214](https://github.com/qossmic/deptrac/pull/214) ([localheinz](https://github.com/localheinz))
- Enhancement: Reference phpunit.xsd as installed with composer [\#213](https://github.com/qossmic/deptrac/pull/213) ([localheinz](https://github.com/localheinz))

## [0.4.0](https://github.com/qossmic/deptrac/tree/0.4.0) (2019-01-11)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.3.0...0.4.0)

**Implemented enhancements:**

- \(re-\)integrate astrunner [\#187](https://github.com/qossmic/deptrac/issues/187)
- Implement a proper --version [\#165](https://github.com/qossmic/deptrac/issues/165)

**Closed issues:**

- Skip violations [\#199](https://github.com/qossmic/deptrac/issues/199)
- Bump minimum PHP version to 7.1 [\#189](https://github.com/qossmic/deptrac/issues/189)
- Building 0.3.0 phar fails [\#188](https://github.com/qossmic/deptrac/issues/188)
- Alpha version release [\#186](https://github.com/qossmic/deptrac/issues/186)
- provide a shim repository [\#185](https://github.com/qossmic/deptrac/issues/185)
- Create a cache to speedup [\#144](https://github.com/qossmic/deptrac/issues/144)
- Cache parsing [\#71](https://github.com/qossmic/deptrac/issues/71)
- add documentation why you should use the phar build [\#50](https://github.com/qossmic/deptrac/issues/50)

**Merged pull requests:**

- extend travis ci config [\#206](https://github.com/qossmic/deptrac/pull/206) ([smoench](https://github.com/smoench))
- enable gz compression + php-scoper [\#204](https://github.com/qossmic/deptrac/pull/204) ([smoench](https://github.com/smoench))
- upgrade to symfony v4.2 [\#203](https://github.com/qossmic/deptrac/pull/203) ([smoench](https://github.com/smoench))
- Improve wording, typos, title case in README [\#201](https://github.com/qossmic/deptrac/pull/201) ([umulmrum](https://github.com/umulmrum))
- Implemented skip violations [\#200](https://github.com/qossmic/deptrac/pull/200) ([dbalabka](https://github.com/dbalabka))
- use git-version as app version + enable php and json compactor [\#198](https://github.com/qossmic/deptrac/pull/198) ([smoench](https://github.com/smoench))
- use progress bar instead of printing a '.' per file [\#197](https://github.com/qossmic/deptrac/pull/197) ([smoench](https://github.com/smoench))
- force strict types [\#196](https://github.com/qossmic/deptrac/pull/196) ([smoench](https://github.com/smoench))
- caching parsed files [\#195](https://github.com/qossmic/deptrac/pull/195) ([smoench](https://github.com/smoench))
- refactor console formatter into subscriber [\#194](https://github.com/qossmic/deptrac/pull/194) ([DavidBadura](https://github.com/DavidBadura))
- improve tests [\#193](https://github.com/qossmic/deptrac/pull/193) ([smoench](https://github.com/smoench))
- integrate Astrunner [\#192](https://github.com/qossmic/deptrac/pull/192) ([smoench](https://github.com/smoench))
- bump to symfony 4.1 [\#191](https://github.com/qossmic/deptrac/pull/191) ([smoench](https://github.com/smoench))
- bump to PHP 7.1 + cleanup [\#190](https://github.com/qossmic/deptrac/pull/190) ([smoench](https://github.com/smoench))

## [0.3.0](https://github.com/qossmic/deptrac/tree/0.3.0) (2018-11-05)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0.2.0...0.3.0)

**Fixed bugs:**

- deptrac fails if user hase no write permission on the binary [\#173](https://github.com/qossmic/deptrac/issues/173)
- DirectoryCollector not working [\#168](https://github.com/qossmic/deptrac/issues/168)

**Closed issues:**

- I would like to use this lib, but it depends on a dev-master lib [\#169](https://github.com/qossmic/deptrac/issues/169)
- `ClassNameCollector` defines its own regex modifiers and delimiters - shouldn't [\#139](https://github.com/qossmic/deptrac/issues/139)
- Add JUnit formatter [\#89](https://github.com/qossmic/deptrac/issues/89)

**Merged pull requests:**

- upgrade php-parser to v4.1.0 [\#184](https://github.com/qossmic/deptrac/pull/184) ([smoench](https://github.com/smoench))
- upgrade symfony to v3.4.17 [\#183](https://github.com/qossmic/deptrac/pull/183) ([smoench](https://github.com/smoench))
- added a changelog [\#182](https://github.com/qossmic/deptrac/pull/182) ([smoench](https://github.com/smoench))
- run tests with php 7.3 [\#180](https://github.com/qossmic/deptrac/pull/180) ([smoench](https://github.com/smoench))
- disable optional output formatter [\#179](https://github.com/qossmic/deptrac/pull/179) ([smoench](https://github.com/smoench))
- Upgrade to Box3 [\#178](https://github.com/qossmic/deptrac/pull/178) ([theofidry](https://github.com/theofidry))
- removed self updater and extended installation documentation [\#177](https://github.com/qossmic/deptrac/pull/177) ([smoench](https://github.com/smoench))
- move box.json to box.json.dist [\#174](https://github.com/qossmic/deptrac/pull/174) ([smoench](https://github.com/smoench))
- upgrade several dependencies [\#172](https://github.com/qossmic/deptrac/pull/172) ([smoench](https://github.com/smoench))
- upgrade astrunner to v1.0 [\#171](https://github.com/qossmic/deptrac/pull/171) ([smoench](https://github.com/smoench))
- Add output formatter to create junit reports [\#167](https://github.com/qossmic/deptrac/pull/167) ([jschaedl](https://github.com/jschaedl))
- upgrade symfony to 3.4.11 [\#166](https://github.com/qossmic/deptrac/pull/166) ([smoench](https://github.com/smoench))
- Rename CollectorFactory to Registry [\#163](https://github.com/qossmic/deptrac/pull/163) ([smoench](https://github.com/smoench))
- upgrade to php-parser 4.0 [\#162](https://github.com/qossmic/deptrac/pull/162) ([smoench](https://github.com/smoench))
- update astrunner [\#161](https://github.com/qossmic/deptrac/pull/161) ([smoench](https://github.com/smoench))
- \#139 - use your own regex pattern [\#160](https://github.com/qossmic/deptrac/pull/160) ([smoench](https://github.com/smoench))
- fix collectors [\#157](https://github.com/qossmic/deptrac/pull/157) ([smoench](https://github.com/smoench))
- move analyzing dependencies to its own class [\#156](https://github.com/qossmic/deptrac/pull/156) ([smoench](https://github.com/smoench))
- move resolving files to be analysed to its own class [\#155](https://github.com/qossmic/deptrac/pull/155) ([smoench](https://github.com/smoench))
- Astrunner: inject EventDispatcher via c'tor [\#154](https://github.com/qossmic/deptrac/pull/154) ([smoench](https://github.com/smoench))
- refactored configuration loading and dumping [\#153](https://github.com/qossmic/deptrac/pull/153) ([smoench](https://github.com/smoench))
- use fqcn as service ids [\#152](https://github.com/qossmic/deptrac/pull/152) ([smoench](https://github.com/smoench))
- improved service configuration [\#151](https://github.com/qossmic/deptrac/pull/151) ([smoench](https://github.com/smoench))
- added analyse alias for AnalyzeCommand [\#150](https://github.com/qossmic/deptrac/pull/150) ([smoench](https://github.com/smoench))

## [0.2.0](https://github.com/qossmic/deptrac/tree/0.2.0) (2018-03-23)

[Full Changelog](https://github.com/qossmic/deptrac/compare/0cb43398db512ae21e6fb4d2fa2c033073a78e3b...0.2.0)

**Implemented enhancements:**

- Enhance the visual progress of generating AstMap [\#114](https://github.com/qossmic/deptrac/issues/114)
- add --self-update to deptrac analyze run [\#99](https://github.com/qossmic/deptrac/issues/99)

**Fixed bugs:**

- Syntax error on php 7.1 [\#113](https://github.com/qossmic/deptrac/issues/113)
- running a global installed deptrac.phar as non-root fails [\#106](https://github.com/qossmic/deptrac/issues/106)
- graphviz formatter should show layers, not just dependencies. [\#11](https://github.com/qossmic/deptrac/issues/11)

**Closed issues:**

- LayerCollector [\#143](https://github.com/qossmic/deptrac/issues/143)
- deptrac.phar -v failing [\#136](https://github.com/qossmic/deptrac/issues/136)
- Limit on external dependencies [\#132](https://github.com/qossmic/deptrac/issues/132)
- Add a version, Tag Releases and allow downloading specific versions [\#130](https://github.com/qossmic/deptrac/issues/130)
- Self return types throw illegal offset warnings [\#126](https://github.com/qossmic/deptrac/issues/126)
- Nullable FQCNs throw illegal offset warnings [\#124](https://github.com/qossmic/deptrac/issues/124)
- PHP Warnings after self-update [\#123](https://github.com/qossmic/deptrac/issues/123)
- Obey HTTP\_PROXY environment variables for selfupdate [\#107](https://github.com/qossmic/deptrac/issues/107)
- Integration with Structure101 [\#104](https://github.com/qossmic/deptrac/issues/104)
- Shorthand analyze options don't work [\#102](https://github.com/qossmic/deptrac/issues/102)
- Add support for AppVeyor [\#92](https://github.com/qossmic/deptrac/issues/92)
- Analyze only one specific file against some deptrac configuration / IDE integration  [\#88](https://github.com/qossmic/deptrac/issues/88)
- print a warning if someone tries to install deptrac as a composer dependency [\#87](https://github.com/qossmic/deptrac/issues/87)
- Collectors configuration [\#79](https://github.com/qossmic/deptrac/issues/79)
- Missing layer [\#75](https://github.com/qossmic/deptrac/issues/75)
- Add a third color [\#74](https://github.com/qossmic/deptrac/issues/74)
- Add a legend [\#73](https://github.com/qossmic/deptrac/issues/73)
- additional configuration required to run 'make build' [\#72](https://github.com/qossmic/deptrac/issues/72)
- Invalid argument in foreach [\#70](https://github.com/qossmic/deptrac/issues/70)
- Sub groups or sublayers [\#68](https://github.com/qossmic/deptrac/issues/68)
- Hide console output for generating AstMap [\#66](https://github.com/qossmic/deptrac/issues/66)
- Directory collector [\#65](https://github.com/qossmic/deptrac/issues/65)
- Support `.depfile.yml` as default configuration file [\#61](https://github.com/qossmic/deptrac/issues/61)
- Download over HTTPS [\#60](https://github.com/qossmic/deptrac/issues/60)
- Options for Graphviz-Formatter are being ignored [\#57](https://github.com/qossmic/deptrac/issues/57)
- Issue with tilde in depfile's exclude\_files [\#52](https://github.com/qossmic/deptrac/issues/52)
- Add support to Symfony 3 [\#48](https://github.com/qossmic/deptrac/issues/48)
- Add a MethodNameCollector [\#42](https://github.com/qossmic/deptrac/issues/42)
- provide phar file [\#41](https://github.com/qossmic/deptrac/issues/41)
- document new formatter cli interface [\#40](https://github.com/qossmic/deptrac/issues/40)
- formatter cli interface [\#32](https://github.com/qossmic/deptrac/issues/32)
- namespace zu SensioLabs\Deptrac [\#30](https://github.com/qossmic/deptrac/issues/30)
- init command vereinfachen [\#28](https://github.com/qossmic/deptrac/issues/28)
- fehler bei zu alter php version [\#27](https://github.com/qossmic/deptrac/issues/27)
- muss ohne graphviz halbwegs laufen [\#26](https://github.com/qossmic/deptrac/issues/26)
- andere lösung für foo/vendors [\#25](https://github.com/qossmic/deptrac/issues/25)
- check composer.lock [\#24](https://github.com/qossmic/deptrac/issues/24)
- cleanup history [\#23](https://github.com/qossmic/deptrac/issues/23)
- 0 Violations should be green [\#22](https://github.com/qossmic/deptrac/issues/22)
- travis support [\#18](https://github.com/qossmic/deptrac/issues/18)
- provide downloadable phar file [\#17](https://github.com/qossmic/deptrac/issues/17)
- add version in code and some kind of usage tracking [\#15](https://github.com/qossmic/deptrac/issues/15)
- add note that deptrac is alpha [\#14](https://github.com/qossmic/deptrac/issues/14)
- self updater [\#13](https://github.com/qossmic/deptrac/issues/13)
- documentation [\#12](https://github.com/qossmic/deptrac/issues/12)
- update phpparser version [\#10](https://github.com/qossmic/deptrac/issues/10)
- add integration tests [\#9](https://github.com/qossmic/deptrac/issues/9)
- use filesystem component in ConfigurationLoader [\#8](https://github.com/qossmic/deptrac/issues/8)
- Inject AstMap to OutputFormatterInterface [\#7](https://github.com/qossmic/deptrac/issues/7)

**Merged pull requests:**

- DirectoryCollector: move tests + cs fixes [\#149](https://github.com/qossmic/deptrac/pull/149) ([smoench](https://github.com/smoench))
- Feature directory collector [\#147](https://github.com/qossmic/deptrac/pull/147) ([timglabisch](https://github.com/timglabisch))
- Added more type-hints including for astrunner type-hints improvement [\#145](https://github.com/qossmic/deptrac/pull/145) ([smoench](https://github.com/smoench))
- type-hints [\#142](https://github.com/qossmic/deptrac/pull/142) ([smoench](https://github.com/smoench))
- Use Null Coalesce Operator [\#140](https://github.com/qossmic/deptrac/pull/140) ([carusogabriel](https://github.com/carusogabriel))
- Drop PHP \<7.0 and HHVM support + upgrade to symfony 3.4 LTS [\#138](https://github.com/qossmic/deptrac/pull/138) ([smoench](https://github.com/smoench))
- use namespaced phpunit TestCase [\#137](https://github.com/qossmic/deptrac/pull/137) ([smoench](https://github.com/smoench))
- Feature: directory collector [\#135](https://github.com/qossmic/deptrac/pull/135) ([jkuchar](https://github.com/jkuchar))
- move tests to top level dir [\#133](https://github.com/qossmic/deptrac/pull/133) ([smoench](https://github.com/smoench))
- allow the "exclude\_files" option to be omitted [\#128](https://github.com/qossmic/deptrac/pull/128) ([xabbuh](https://github.com/xabbuh))
- Prevent illegal offset warnings return types [\#127](https://github.com/qossmic/deptrac/pull/127) ([hiddeco](https://github.com/hiddeco))
- Prevent illegal offset warnings FQCN [\#125](https://github.com/qossmic/deptrac/pull/125) ([hiddeco](https://github.com/hiddeco))
- Fixed parsing of nullable return types [\#122](https://github.com/qossmic/deptrac/pull/122) ([hiddeco](https://github.com/hiddeco))
- Fix minor misprints [\#121](https://github.com/qossmic/deptrac/pull/121) ([bocharsky-bw](https://github.com/bocharsky-bw))
- Emit method return types. [\#120](https://github.com/qossmic/deptrac/pull/120) ([dbrumann](https://github.com/dbrumann))
- fix typo [\#119](https://github.com/qossmic/deptrac/pull/119) ([meandmymonkey](https://github.com/meandmymonkey))
- Remove windows build CI-pipeline. [\#118](https://github.com/qossmic/deptrac/pull/118) ([dbrumann](https://github.com/dbrumann))
- Update nikic/php-parser to 3.0. [\#117](https://github.com/qossmic/deptrac/pull/117) ([dbrumann](https://github.com/dbrumann))
- allow PHPUnit 5 too for forward compatibility [\#116](https://github.com/qossmic/deptrac/pull/116) ([xabbuh](https://github.com/xabbuh))
- detect static method call and property access deps [\#115](https://github.com/qossmic/deptrac/pull/115) ([xabbuh](https://github.com/xabbuh))
- Update path in command example in README [\#110](https://github.com/qossmic/deptrac/pull/110) ([richardmiller](https://github.com/richardmiller))
- Fix composer file permissions [\#100](https://github.com/qossmic/deptrac/pull/100) ([amansilla](https://github.com/amansilla))
- Add tests for self update command [\#98](https://github.com/qossmic/deptrac/pull/98) ([amansilla](https://github.com/amansilla))
- Sort composer packages [\#97](https://github.com/qossmic/deptrac/pull/97) ([amansilla](https://github.com/amansilla))
- Add appveyor status to readme [\#96](https://github.com/qossmic/deptrac/pull/96) ([amansilla](https://github.com/amansilla))
- Add description to composer file [\#95](https://github.com/qossmic/deptrac/pull/95) ([amansilla](https://github.com/amansilla))
- drop no longer needed repositories [\#94](https://github.com/qossmic/deptrac/pull/94) ([xabbuh](https://github.com/xabbuh))
- Add support for Appveyor [\#93](https://github.com/qossmic/deptrac/pull/93) ([amansilla](https://github.com/amansilla))
- Remove composer deprecated option --dev [\#91](https://github.com/qossmic/deptrac/pull/91) ([amansilla](https://github.com/amansilla))
- use variables for binaries in Makefile [\#85](https://github.com/qossmic/deptrac/pull/85) ([ckressibucher](https://github.com/ckressibucher))
- Add self-update command [\#84](https://github.com/qossmic/deptrac/pull/84) ([amansilla](https://github.com/amansilla))
- Fix build php.ini documentation [\#83](https://github.com/qossmic/deptrac/pull/83) ([amansilla](https://github.com/amansilla))
- fixed typo [\#82](https://github.com/qossmic/deptrac/pull/82) ([rokde](https://github.com/rokde))
- update Graphviz dependency for Windows [\#81](https://github.com/qossmic/deptrac/pull/81) ([maxime-pasquier](https://github.com/maxime-pasquier))
- Add PHPDoc for CollectorInterface [\#80](https://github.com/qossmic/deptrac/pull/80) ([theofidry](https://github.com/theofidry))
- Review of OutputFormatterInterface [\#78](https://github.com/qossmic/deptrac/pull/78) ([theofidry](https://github.com/theofidry))
- Reduces verbosity when generating AstMap. [\#67](https://github.com/qossmic/deptrac/pull/67) ([dbrumann](https://github.com/dbrumann))
- Added a warning about formatter-options and default command. [\#64](https://github.com/qossmic/deptrac/pull/64) ([dbrumann](https://github.com/dbrumann))
- Add license to Composer [\#63](https://github.com/qossmic/deptrac/pull/63) ([theofidry](https://github.com/theofidry))
- Add ToC [\#62](https://github.com/qossmic/deptrac/pull/62) ([theofidry](https://github.com/theofidry))
- Analysis with violations returns a 0 exit code. [\#59](https://github.com/qossmic/deptrac/pull/59) ([dbrumann](https://github.com/dbrumann))
- Add travis build status to readme [\#58](https://github.com/qossmic/deptrac/pull/58) ([amansilla](https://github.com/amansilla))
- Typo in README.md [\#55](https://github.com/qossmic/deptrac/pull/55) ([oliveradria](https://github.com/oliveradria))
- Fixes \#52 with null exclude\_files. [\#54](https://github.com/qossmic/deptrac/pull/54) ([dbrumann](https://github.com/dbrumann))
- Add travis-ci integration [\#53](https://github.com/qossmic/deptrac/pull/53) ([amansilla](https://github.com/amansilla))
- Fixed typo in filename [\#51](https://github.com/qossmic/deptrac/pull/51) ([dbrumann](https://github.com/dbrumann))
- Fix typo in curl command [\#47](https://github.com/qossmic/deptrac/pull/47) ([icambridge](https://github.com/icambridge))
- method collector [\#46](https://github.com/qossmic/deptrac/pull/46) ([slde-flash](https://github.com/slde-flash))
- Fix typos in documentation [\#45](https://github.com/qossmic/deptrac/pull/45) ([mre](https://github.com/mre))



\* *This Changelog was automatically generated by [github_changelog_generator](https://github.com/github-changelog-generator/github-changelog-generator)*
