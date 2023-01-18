# Extending Deptrac

Deptrac defines its extension points by providing a set of **contract** classes
that you can depend on for your implementation. The classes can be found in
the `src/Contract` directory and are covered by
the [backwards compatibility policy promise](bc_policy.md), meaning the will stay stable within major releases.

There are several ways you can extend Deptrac:

- [Output formatters](#Output Formatters) - To change how the output of
  dependency analysis is formatted. This might be useful to integrate Deptrac
  into your CI/CD pipeline.
- [Analyser Events](#Analyser Events) - Decide whether a dependency is
  Uncovered, Allowed, Violation or SkippedViolation.
- [Layer collectors](#Layer collectors) - Add custom collectors for organizing your code into layers

> **Note**
> In examples where FQCN is not specified, the base
> namespace `Qossmic\Deptrac\Contract\` is omitted for readability.

## Output Formatters

Creating an output formatter requires creating a new class implementing
the `OutputFormatter\OutputFormatterInterface` and
register it
in your `deptrac.yaml` file like this:

```yaml
services:
  - class: App\DeptracExtension\MyCustomOutputFormatter
    autowire: true
    tags:
      - output_formatter
```

And you are done. You can call your formatter by using the `-f` or `--formatter`
CLI flag with the name you defined
in the `getName()`-method of your forrmatter.

## Analyser Events

The deptrac analyser creates a `Analyser\ProcessEvent` for each dependency it
finds. You
can listen for this event and decide how to handle it depending on the
details of the dependency.

This might be useful when you want to ignore a specific group of
violations in testing-related code for example. First you create a class to process
this event:

```php
class IgnoreDependenciesOnShouldNotHappenException
{
    public function __invoke(ProcessEvent $event): void
    {
        if ("Qossmic\Deptrac\Supportive\ShouldNotHappenException" === $event->dependentReference->getToken()->toString()) {
            $event->stopPropagation();
        }
    }
}
```

And then you register this class in your `deptrac.yaml` file:

```yaml
services:
  - class: IgnoreDependenciesOnShouldNotHappenException
    tags:
      - { name: kernel.event_listener, event: Qossmic\Deptrac\Contract\Analyser\ProcessEvent }
```

You can also en masse change the whole result set by instead listening to
the `Analyser\PostProcessEvent`. This allows you to add Rules (Uncovered,
Allowed, Violation, SkippedViolation),
Warnings and Errors to the analysis result. Do so by calling
the `replaceResult()` method.

## Layer collectors

Deptrac already comes with a comprehensive list of [collectors](collectors.md). If you
need something more specific, you can write your own collector
implementing `Layer\CollectorInterface` and register it in your `deptrac.yaml`
file:

```yaml
services:
  - class: App\DeptracExtension\MyCustomLayerCollector
    autowire: true
    tags:
      - { name: 'collector', type: '<name to use in the deptrac.yaml to invoke the collector>' }
```

## List of well-known user extension

- [Deptrac-awesome](https://packagist.org/packages/dance-engineer/deptrac-awesome)
    - custom commands and output formatters
