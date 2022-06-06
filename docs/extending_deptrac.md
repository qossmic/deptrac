# Extending Deptrac

There are several ways you can extend Deptrac:

- [Output formatters](#Output Formatters) - To change how the output of
  dependency analysis is formatted. This might be useful to integrate Deptrac
  into your CI/CD pipeline.
- [Commands](#Commands) - Take advantage of the Deptrac framework to analyse
  different aspects of your dependency tree.

Apart from those, Deptrac also offers many
other [extension points](#Other extension points) you can hook up into.

## Output Formatters

Creating an output formatter requires only to create a new class implementing
the `Qossmic\Deptrac\OutputFormatter\OutputFormatterInterface` and register it
in your `deptrac.yaml` file like this:

```yaml
services:
  - class: <Your FQCN>
    autowire: true
    tags:
      - output_formatter
```

And you are done. You can call your formatter by using the `-f` or `--formatter`
CLI flag with the name you defined in `OutputFormatterInterface::getName()`.

## Commands

Creating a command requires only to create a new class implementing
the `Symfony\Component\Console\Command\Command` and register it
in your `deptrac.yaml` file like this:

```yaml
services:
  - class: <Your FQCN>
    autowire: true
    tags:
      - console.command
```

And you are done. You can call your command by using the command name as it is
custom for Symfony commands.

## Other extension points

### Interfaces

#### DependencyEmitterInterface (Qossmic\Deptrac\Dependency\Emitter)

Dependency emitters allow you to create new dependencies between tokens. To
register an emitter, add this to your `deptrac.yaml` file:

```yaml
services:
  - class: <Your FQCN>
    autowire: true
    tags:
      - dependency_emitter
```

The registered emitter then can be used by adding the string from `::getAlias()`
to the config's file `analyser.types` section:

```yaml
deptrac:
  analyser:
    types:
      - <string from ::getAlias()>
```

#### CollectorInterface (Qossmic\Deptrac\Layer\Collector)

A collector is responsible to tell whether an AST node (e.g. a specific class)
is part of a layer. This is the way all the [collectors](collectors.md) in
deptrac are implemented.

To register a collector, ad this into your `deptrac.yaml` file:

```yaml
  - class: <Your FQCN>
    autowire: true
    tags:
      - { name: 'collector', type: '<name to use in the deptrac.yaml to invoke the collector>' }
```

### Events

[Event listeners](https://symfony.com/doc/current/event_dispatcher.html#creating-an-event-listener)
allow you to listen to important events that happen in Deptrac as it is
processing your command. For some events, you can modify the response to alter
Deptrac behavior. For other, you can just collect the content of the event and
process it later yourself.

#### Ast Events (Qossmic\Deptrac\Ast\Event)

Allows you to monitor the AST parsing process.

- `PreCreateAstMapEvent` - Called before analysis of AST of files has started
- `AstFileAnalysedEvent` - Called after a file has been successfully parsed and
  AST map created
- `AstFileSyntaxErrorEvent` - Called if there is syntax error in the analysed
  file
- `PostCreateAstMapEvent` - Called after the full AST map has been created

#### Dependency Events (Qossmic\Deptrac\Dependency\Event)

Allows you to monitor the dependency generation process.

- `PreEmitEvent` - Called before a `DependencyEmitter` starts adding
  dependencies
- `PostEmitEvent` - Called after a `DependencyEmitter` has finished adding
  dependencies
- `PreFlattenEvent` - Called before inheritance dependencies are resolved
- `PostFlattenEvent` - Called after inheritance dependencies are resolved

#### Analyser Events (Qossmic\Deptrac\Analyser\Event)

Allows you to add Rules(Uncovered, Allowed, Violation, SkippedViolation),
Warnings and Errors to the analysis result. Do so by calling
the `::replaceResult()` method.

- `ProcessEvent` - Called for each dependency and each depender layer.
- `PostProcessEvent` - Called once at the end of the analysis.

## List of well-known user extension

- [Deptrac-awesome](https://packagist.org/packages/dance-engineer/deptrac-awesome)
    - custom commands and output formatters

## Future extension points (not currently supported)

#### LayerResolverInterface (LayerResolverInterface)

Layer resolvers decide in which layer a `TokenReference` can be found in.

#### InputCollectorInterface (Qossmic\Deptrac\InputCollector)

Input collector gives you an alternative way to collect files for analysis, if
the default `paths` section of the `deptrac.yaml` is not sufficient for your
needs.
