# Collectors

Collectors decide if a node (typically a class) is part of a layer. You can use
multiple different collectors for a layer.

* [`bool` Collector](#bool-collector)
* [`className` Collector](#classname-collector)
* [`classNameRegex` Collector](#classnameregex-collector)
* [`directory` Collector](#directory-collector)
* [`extends` Collector](#extends-collector)
* [`implements` Collector](#implements-collector)
* [`inherits` Collector](#inherits-collector)
* [`method` Collector](#method-collector)
* [`uses` Collector](#uses-collector)
* [`functionName` Collector](#functionname-collector)
* [`superglobal` Collector](#superglobal-collector)
* [Custom Collectors](#custom-collectors)

## `bool` Collector

The `bool` collector allows combining other collectors with or without negation.

```yml
layers:
    -   name: Asset
        collectors:
            -   type: bool
                must:
                    -   type: className
                        regex: .*Foo\\.*
                    -   type: className
                        regex: .*\\Asset.*
                must_not:
                    -   type: className
                        regex: .*Assetic.*
```

Every class contains `Foo\` AND `\Asset` and NOT `Assetic`, will become a part
of the *Asset* layer.

## `className` Collector

The `className` collector allows collecting classes by matching their fully
qualified name to a simplified regular expression. Any matching class will be
added to the assigned layer.

```yaml
layers:
    -   name: Controller
        collectors:
            -   type: className
                regex: .*Controller.*
```

Every class name that matches the regular expression becomes a part of the
*controller* layer. This collector has predefined delimiters and
modifier: `/YOUR_EXPRESSION/i`

## `classNameRegex` Collector

The `classNameRegex` collector allows collecting classes by matching their fully
qualified name to a regular expression. Any matching class will be added to the
assigned layer.

```yaml
layers:
    -   name: Controller
        collectors:
            -   type: classNameRegex
                regex: '#.*Controller.*#'
```

Every class name that matches the regular expression becomes a part of the
*controller* layer.

## `directory` Collector

The `directory` collector allows collecting classes by matching their file path
they are declared in to a simplified regular expression. Any matching class will
be added to the assigned layer.

```yaml
layers:
    -   name: Controller
        collectors:
            -   type: directory
                regex: src/Controller/.*
```

Every file path that matches the regular expression `src/Controller/.*` becomes
a part of the *controller* layer. This collector has predefined delimiters and
modifier: `#YOUR_EXPRESSION#i`

## `extends` Collector

The `extends` collector allows collecting classes extending a specified class by
matching recursively for a fully qualified class or interface name.

```yaml
layers:
    -   name: Foo
        collectors:
            -   type: extends
                extends: 'App\SomeClass'
```

## `implements` Collector

The `implements` collector allows collecting classes implementing a specified
interface by matching recursively for a fully qualified interface name.

```yaml
layers:
    -   name: Foo
        collectors:
            -   type: implements
                implements: 'App\SomeInterface'
```

## `inherits` Collector

The `inherits` collector allows collecting classes inheriting from a specified
class, whether by implementing an interface, extending another class or by using
a trait, by matching recursively for a fully qualified class name.

```yaml
layers:
    -   name: Foo
        collectors:
            -   type: inherits
                inherits: 'App\SomeInterface'
```

## `method` Collector

The `method` collector allows collecting classes by matching their methods name
to a regular expression. Any matching class will be added to the assigned layer.

```yaml
layers:
    -   name: Foo services
        collectors:
            -   type: method
                name: .*foo
```

Every class having a method that matches the regular expression `.*foo`,
e.g. `getFoo()` or `setFoo()` becomes a part of the *Foo services* layer.

## `uses` Collector

The `uses` collector allows collecting classes using a specified trait by
matching recursively for a fully qualified trait name.

```yaml
layers:
    -   name: Foo
        collectors:
            -   type: uses
                uses: 'App\SomeTrait'
```

## `functionName` Collector

The `functionName` collector allows collecting functions by matching their fully
qualified name to a simplified regular expression. Any matching function will be
added to the assigned layer.

```yaml
layers:
    -   name: Foo
        collectors:
            -   type: functionName
                regex: .*array_.*
```

## `superglobal` Collector

The `superglobal` collector allows collecting superglobal PHP variables
matching the specified superglobal name.

```yaml
layers:
    -   name: Foo
        collectors:
            -   type: superglobal
                names:
                  - _POST
                  - _GET
```

## Custom Collectors

You can create custom collectors in your project by implementing the
`Qossmic\Deptrac\Collector\CollectorInterface`. As soon as an unknown collector
is referenced in the config file Deptrac will try to load the class in your
project. With this you can create collectors specific for your use case.

If you would like to make your collector available to others, feel free to
[contribute](contributing.md) it by making a pull request.
