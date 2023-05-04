# Debugging

Deptrac provides a series of debug commands that help you identify issues in
your config files. All commands output one issue per line and can therefore be
easily combined with other tools like `wc` or `grep`.

## `debug:layer`

With the `debug:layer`-command you can list all tokens which are matched in
a specific layer. This command only shows tokens that would be emitted by your analyser configuration.

```bash
php deptrac.phar debug:layer --config-file=examples/DirectoryLayer.depfile.yaml Layer1

examples\Layer1\AnotherClassLikeAController
examples\Layer1\SomeClass
examples\Layer1\SomeClass2
```

## `debug:token`

The `debug:token` (previously `debug:class-like`)-command will let you know which layers a specified token belongs to. Since you can specify the token type, this commands ignores your analyser configuration for emitted token types.

```bash
php deptrac.phar debug:token --config-file=examples/DirectoryLayer.depfile.yaml 'examples\Layer1\AnotherClassLikeAController' class-like

Controller
Layer1
```

## `debug:unassigned`

With the `debug:unassigned`-command you list all tokens in your path that are
not assigned to any layer. This is useful to test that your collector
configuration for layers is correct.  This command only shows tokens that would be emitted by your analyser configuration.

```bash
php deptrac.phar debug:unassigned --config-file=examples/DirectoryLayer.depfile.yaml

examples\Layer1\AnotherClassLikeAController
examples\Layer1\SomeClass
examples\Layer1\SomeClass2
```

## `debug:dependencies`

With the `debug:dependencies`-command you can see all dependencies of your layer. You can optionally specify a target layer to get only dependencies from one layer to the other:

```bash
php deptrac.phar debug:dependencies debug:dependencies Ast InputCollector

  Qossmic\Deptrac\Core\Ast\AstMapExtractor depends on Qossmic\Deptrac\Core\InputCollector\InputCollectorInterface (InputCollector)
  .../deptrac/src/Core/Ast/AstMapExtractor.php:15
  Qossmic\Deptrac\Core\Ast\AstMapExtractor depends on Qossmic\Deptrac\Core\InputCollector\InputException (InputCollector)
  .../deptrac/src/Core/Ast/AstMapExtractor.php:28
  Qossmic\Deptrac\Core\Ast\AstException depends on Qossmic\Deptrac\Core\InputCollector\InputException (InputCollector)
  .../deptrac/src/Core/Ast/AstException.php:13
```

## `debug:unused`

With the `debug:unused`-command you list all the rulesets that are not being used (i.e. there are no dependencies being allowed by this ruleset).

You can optionally specify a limit (`--limit=<int>`) of how many times can be the ruleset used to be considered unused. This is useful
if you want to find dependencies that are barely used and may be a prime candidate to get rid of.

```bash
php deptrac.phar debug:unused --limit=10

  Analyser layer is dependent Layer layer 5 times
  Ast layer is dependent File layer 9 times
  Ast layer is dependent InputCollector layer 3 times
  Console layer is dependent OutputFormatter layer 4 times
  Console layer is dependent DependencyInjection layer 2 times
  Console layer is dependent File layer 5 times
  InputCollector layer is dependent File layer 3 times
  OutputFormatter layer is dependent DependencyInjection layer 1 times
```
