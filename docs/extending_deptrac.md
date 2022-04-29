# Extending Deptrac

There are 2 main ways you can extend Deptrac:

- [Output formatters](#Output Formatters) - To change how the output of dependency analysis is formatted. This might be useful to integrate Deptrac into your CI/CD pipeline.
- [Commands](#Commands) - Take advantage of the Deptrac framework to analyse different aspects of your dependency tree.

Apart from those, Deptrac also offers many other [extension points](#Other extension points) you can hook up into.

## Output Formatters

Creating an output formatter is very simple. Create a new class implementing the `Qossmic\Deptrac\OutputFormatter\OutputFormatterInterface` and register it in your `deptrac.yaml` file like this:

```yaml
services:
  - class: <Your FQCN>
    autowire: true
    tags:
      - output_formatter
```

And you are done. You can call your formatter by using the `-f` or `--formatter` CLI flag with the name you defined in `OutputFormatterInterface::getName()`.

## Commands

TODO

### Analyser Events

- `ProcessEvent`
- `PostPorcessEvent`

### Ast Events

- `PreCreateAstMapEvent`
- `AstFileAnalysedEvent`
- `AstFileSyntaxErrorEvent`
- `PostCreateAstMapEvent`

### Dependency Events

- `PreEmitEvent`
- `PostEmitEvent`
- `PreFlattenEvent`
- `PostFlattenEvent`

## Other extension points

TODO

## List of well-known extension

- Deptrac-awesome
  - https://packagist.org/packages/dance-engineer/deptrac-awesome
