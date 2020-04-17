# Deptrac

![](https://github.com/sensiolabs-de/deptrac/workflows/Continuous%20Integration/badge.svg?branch=master)

## What is Deptrac

Deptrac is a static code analysis tool that helps to enforce rules for dependencies between software layers in your PHP projects.

For example, you can define a rule like "controllers may not depend on models".
To ensure this, deptrac analyzes your code to find any usages of models in your controllers and will show you where
this rule was violated.

![ModelController1](examples/ControllerServiceRepository1.png)

## Table of Contents

1. [Introduction Video](#introduction-video)
1. [Getting Started](#getting-started)
    1. [The Depfile](#the-depfile)
    1. [Explanation](#explanation)
1. [Installation](#installation)
    1. [PHAR](#phar)
    1. [Composer](#composer)
    1. [PHIVE](#phive)
    1. [Optional Dependency: Graphviz](#optional-dependency-graphviz)
1. [Run Deptrac](#run-deptrac)
1. [Layers](#layers)
    1. [Collecting Layers](#collecting-layers)
1. [Violations](#violations)
1. [Ruleset (Allowing Dependencies)](#ruleset-allowing-dependencies)
1. [Different Layers and Different Views](#different-layers-and-different-views)
1. [Collectors](#collectors)
    1. [`className` Collector](#classname-collector)
    1. [`classNameRegex` Collector](#classnameregex-collector)
    1. [`directory` Collector](#directory-collector)
    1. [`bool` Collector](#bool-collector)
    1. [`method` Collector](#method-collector)
    1. [`implements` Collector](#implments-collector)
    1. [More Collectors](#more-collectors)
1. [Formatters](#formatters)
    1. [Console Formatter](#console-formatter)
    1. [Graphviz Formatter](#graphviz-formatter)
    1. [JUnit Formatter](#junit-formatter)
1. [Build Deptrac](#build-deptrac)
1. [Contribute](#contribute)

## Introduction Video

[![ScreenShot](examples/youtube.png)](https://www.youtube.com/watch?v=T5oWc4ujmOo)

## Getting Started

The easiest way to get started is to download the latest [deptrac.phar](https://github.com/sensiolabs-de/deptrac/releases).

At first, you need a so called *depfile*, which is written in YAML.
You can generate a bootstrapped `depfile.yml` with

```bash
php deptrac.phar init
```

In this file you define (mainly) three things:

1. The location of your source code.
2. The layers of your application.
3. The allowed dependencies between your layers.


### The Depfile

Let's have a look at the generated file:

```yaml
# depfile.yml
paths:
  - ./src
exclude_files:
  - .*test.*
layers:
  - name: Controller
    collectors:
      - type: className
        regex: .*Controller.*
  - name: Repository
    collectors:
      - type: className
        regex: .*Repository.*
  - name: Service
    collectors:
      - type: className
        regex: .*Service.*
ruleset:
  Controller:
    - Service
  Service:
    - Repository
  Repository: ~
```


#### Explanation

In the first section, `paths`, you declare where deptrac should look for your code.
As this is an array of directories, you can specify multiple locations.

With the `exclude_files` section, you can specify one or more regular expressions for files that should be excluded,
the most common being probably anything containing the "test" word in the path.

We defined three `layers` in the example: *Controller*, *Repository* and *Service*.
Deptrac is using so called `collectors` to group classes into `layers` (in this case by the name of the class).

The `ruleset` section defines, how these layers may or may not depend on other layers.
In the example, every class of the *Controller* layer may depend on classes that reside in the *Service* layer,
and classes in the *Service* layer may depend on classes in the *Repository* layer.

Classes in the *Repository* layer may NOT depend on any classes in other layers.
The `ruleset` acts as a whitelist, therefore the *Repository* layer rules can be omitted, however
explicitly stating that the layer may not depend on other layers is more declarative.

If a class in the *Repository* layer uses a class in the *Service* layer, deptrac will recognize the dependency
and raises a violation for this case. The same counts if a *Service* layer class uses a *Controller* layer class.


## Installation

### PHAR

Download the latest [deptrac.phar](https://github.com/sensiolabs-de/deptrac/releases).
 
Run it using `php deptrac.phar` or feel free to add it to your PATH (i.e. `/usr/local/bin/deptrac`)

```bash
curl -LS https://github.com/sensiolabs-de/deptrac/releases/download/0.6.0/deptrac.phar -o deptrac.phar

# optional
sudo chmod +x deptrac.phar
sudo mv deptrac.phar /usr/local/bin/deptrac
```

(In this guide, we assume, you have the [deptrac.phar](https://github.com/sensiolabs-de/deptrac/releases) in your project root)

### Composer

We do not recommend installing this repository via Composer. Instead please use the dedicated distribution repository https://github.com/sensiolabs-de/deptrac-shim.

### PHIVE

You can install Deptrac with [Phive](https://phar.io/#Install)

`phive install -g sensiolabs-de/deptrac`

and accept the key with fingerprint `088B 7289 7980 555C 6E4E F669 3C52 E7DE D5E2 D9EE`

To upgrade Deptrac use the following command:

`phive update -g sensiolabs-de/deptrac`

### Optional Dependency: Graphviz

If you want to create graphical diagrams with your class dependencies, you will also need the `dot` command provided by [Graphviz](http://www.graphviz.org/).
Graphviz can be installed using common package managers:

```bash
# for osx + brew
brew install graphviz

# for ubuntu and debian
sudo apt-get install graphviz
```

Graphviz is also available for [Windows](https://graphviz.gitlab.io/_pages/Download/Download_windows.html): Install the current stable release and append the binary path on the environment variable Path (like ``C:\Program Files (x86)\Graphviz2.38\bin``).


## Run Deptrac

To execute deptrac, run

```bash
php deptrac.phar

# which is equivalent to
php deptrac.phar analyze depfile.yml
```

If you run `php deptrac.phar -v` you'll get a more verbose output.

The analyse command runs with a caching mechanism for parsed files by default. This could be disabled with the `--no-cache` option.


## Layers

Deptrac allows you to group different classes into *layers*.
Technically layers are nothing more than a collection of classes.

Each layer has a unique name and a list of one or more collectors, which will look for classes that should be assigned to this layer (and yes, classes can be assigned to more than one layer).

(Hopefully) most software is written with some kind of layers in mind.
For example a typical MVC application has at least controllers, models and views.

Deptrac allows you to visualize and enforce rulesets, based on such layer information.

So you could define that every class that ends with `Controller` will be assigned to the *Controller* layer, and
every class that has `\Model\` in its namespace will be added to the *Model* layer.

Say you are adopting MVC, most of the time you do not want your models to access controllers, but it is allowed for controllers
to access models. Deptrac allows you to enforce and visualize these dependencies/rules.

**By default, all dependencies between layers are forbidden!**


### Collecting Layers

If your application has *controllers* and *models*, deptrac allows you to
group them into layers.

```yaml
paths:
  - ./examples/ModelController
exclude_files: ~
layers:
  - name: Models
    collectors:
      - type: className
        regex: .*MyNamespace\\Models\\.*
  - name: Controller
    collectors:
      - type: className
        regex: .*MyNamespace\\.*Controller.*
ruleset: ~
```

At first, lets take a closer look at the first layer (named *Models*).

Here we decided that our software has some kind of layer called *Models*.
You assign classes to this layer with the help of [*Collectors*](#collectors).

Collectors are responsible for taking a closer look at your code and decide if a class is part of a layer.
By using the `className` collector you can define a regular expression for a class name.
Every (fully qualified) class name that matches this regular expression becomes part of the assigned layer.
In this example we define that every class that contains `MyNamespace\Models\` will be a part of the *Model* layer.

Every class that matches `.*MyNamespace\\.*Controller.*` will become a part of the *Controller* layer.

As we defined our layers, we can generate a dependency graph for the example configuration:
(Make sure that [*Graphviz*](#optional-dependency-graphviz) (dot) is installed on your system)

```bash
php deptrac.php analyze examples/ModelController1.depfile.yml
```

After deptrac has finished, an image should be opened:

![ModelController1](examples/ModelController1.png)

On your command line deptrac will produce this output:

```bash
Start to create an AstMap for 2 Files.
..
AstMap created.
start emitting dependencies "InheritanceDependencyEmitter"
start emitting dependencies "BasicDependencyEmitter"
end emitting dependencies
start flatten dependencies
end flatten dependencies
collecting violations.
formatting dependencies.

Found 0 Violations
```

The output shows, that deptrac is parsing 2 files and found 0 violations.
By default every dependency between layers is a violation.
In our case there are (for now) no dependencies between our classes (layers).
So it's fine that deptrac will show us 2 independent layers without any relationship.


## Violations

If we have 2 layers (*Models*, *Controller*) and one layer is using the other, deptrac will raise a violation by default:

```php
// see the example in examples/ModelController2
namespace examples\MyNamespace\Controllers;

use examples\MyNamespace\Models\SomeModel;

class SomeController
{
    public function foo(SomeModel $m) {
        return $m;
    }
}
```

After running deptrac for this example

```bash
php deptrac.php analyze examples/ModelController2.depfile.yml
```

we will get this output:

```bash
Start to create an AstMap for 2 Files.
..
AstMap created.
start emitting dependencies "InheritanceDependencyEmitter"
start emitting dependencies "BasicDependencyEmitter"
end emitting dependencies
start flatten dependencies
end flatten dependencies
collecting violations.
formatting dependencies.
examples\MyNamespace\Controllers\SomeController::5 must not depend on examples\MyNamespace\Models\SomeModel (Controller on Models)
examples\MyNamespace\Controllers\SomeController::9 must not depend on examples\MyNamespace\Models\SomeModel (Controller on Models)

Found 2 Violations
```

![ModelController1](examples/ModelController2.png)

Deptrac has found two violations because the relation from the controller to model layers is not allowed.
The console output shows exactly the lines deptrac found.

### Skip violations

Deptrac integration into existing CI/CD pipeline might be difficult because of existing dependency violations in the code.
In this case, you can skip existing violations to gradually improve your code and avoid possibility introduce any new violations.

Violations can be skipped by provided list of dependencies in *skip_violations* configuration section:
```yaml
skip_violations:
  Library\LibClass:
    - Core\CoreClass
``` 
*skip_violations* section contains associative array where a key (`Library\LibClass`) is the name of dependant class 
and values (`Core\CoreClass`) are dependency classes.

Matched violations will be marked as skipped:
```bash
php deptrac.php analyze examples/SkipViolations.yml
```
```text
Start to create an AstMap for 1 Files.
 ..
AstMap created.
start emitting dependencies "InheritanceDependencyEmitter"
start emitting dependencies "BasicDependencyEmitter"
end emitting dependencies
start flatten dependencies
end flatten dependencies
collecting violations.
formatting dependencies.
[SKIPPED] Library\LibClass::11 must not depend on Core\CoreClass (Library on Core)

Found 0 Violations and 1 Violations skipped
``` 


## Ruleset (Allowing Dependencies)

Allowed dependencies between layers are configured in *rulesets*.

By default deptrac will raise a violation for every dependency between layers.
In real software you want to allow dependencies between different kinds of layers.

As a lot of architectures define some kind of *controllers*, *services* and *repositories*, a natural approach for this would be to define these rules:

- *Controllers* may access *services*, but not *repositories*.
- *Services* may access *repositories*, but not *controllers*.
- *Repositories* neither may access services nor *controllers*.

We can define this using the following depfile:

```yaml
paths:
  - ./examples/ControllerServiceRepository1/
exclude_files: ~
layers:
  - name: Controller
    collectors:
      - type: className
        regex: .*MyNamespace\\.*Controller.*
  - name: Repository
    collectors:
      - type: className
        regex: .*MyNamespace\\.*Repository.*
  - name: Service
    collectors:
      - type: className
        regex: .*MyNamespace\\.*Service.*
ruleset:
  Controller:
    - Service
  Service:
    - Repository
  Repository: ~
```

Take a closer look at the ruleset.
We whitelist that *Controller* can access *Service* and *Service* can access *Repository*.

After running deptrac we will get this result:

![ModelController1](examples/ControllerServiceRepository1.png)

```bash
Start to create an AstMap for 3 Files.
...
AstMap created.
start emitting dependencies "InheritanceDependencyEmitter"
start emitting dependencies "BasicDependencyEmitter"
end emitting dependencies
start flatten dependencies
end flatten dependencies
collecting violations.
formatting dependencies.
examples\MyNamespace\Repository\SomeRepository::5 must not depend on examples\MyNamespace\Controllers\SomeController (Repository on Controller)
```

Deptrac now finds a violation.
If we take a closer look at the "SomeRepository" on line 5, we will see an unused use statement for a controller:

```php
namespace examples\MyNamespace\Repository;

use examples\MyNamespace\Controllers\SomeController;

class SomeRepository { }
```

If we remove the `use` statement and rerun deptrac, the violation will disappear.


## Different Layers and Different Views

In the example above we defined 3 different layers (*controller*, *repository* and *service*).
Deptrac gives architects the power to define what kind of layers exist.

Typically usecases are:

- caring about layers in different architectures (tier, hexagonal, ddd, ...)
- caring about dependencies between different kinds of services (infrastructure services / domain services / entities / DTOs / ...)
- caring about coupling to third party code like composer vendors, frameworks, ...
- enforcing naming conventions
- ...

Typically software has more than just one view.
**It is possible to use multiple depfiles, to take care about different architectural views.**


## Collectors

Collectors decide if a node (typically a class) is part of a layer.
Deptrac will support more collectors out of the box and will provide an
easy way to extend deptrac with custom collectors.

Technically, deptrac creates an [AST](https://en.wikipedia.org/wiki/Abstract_syntax_tree) from your code and groups nodes to different layers.


### `className` Collector

The `className` collector allows collecting classes by matching their fully qualified name to a simplified regular expression.
Any matching class will be added to the assigned layer.

```yaml
layers:
  - name: Controller
    collectors:
      - type: className
        regex: .*Controller.*
```

Every class name that matches the regular expression becomes a part of the *controller* layer.
This collector has predefined delimiters and modifier: `/YOUR_EXPRESSION/i`


### `classNameRegex` Collector

The `classNameRegex` collector allows collecting classes by matching their fully qualified name to a regular expression.
Any matching class will be added to the assigned layer.

```yaml
layers:
  - name: Controller
    collectors:
      - type: classNameRegex
        regex: #.*Controller.*#
```

Every class name that matches the regular expression becomes a part of the *controller* layer.


### `directory` Collector

The `directory` collector allows collecting classes by matching their file path they are declared in to a simplified regular expression.
Any matching class will be added to the assigned layer.

```yaml
layers:
  - name: Controller
    collectors:
      - type: directory
        regex: src/Controller/.*
```

Every file path that matches the regular expression `src/Controller/.*` becomes a part of the *controller* layer.
This collector has predefined delimiters and modifier: `#YOUR_EXPRESSION#i`


### `bool` Collector

The `bool` collector allows combining other collectors with or without negation.

```yml
layers:
  - name: Asset
    collectors:
      - type: bool
        must:
          - type: className
            regex: .*Foo\\.*
          - type: className
            regex: .*\\Asset.*
        must_not:
          - type: className
            regex: .*Assetic.*
```

Every class that contains `Foo\` AND `\Asset` and NOT `Assetic`, will become a part of the *Asset* layer.


### `method` Collector

The `method` collector allows collecting classes by matching their methods name to a regular expression.
Any matching class will be added to the assigned layer.

```yaml
layers:
  - name: Foo services
    collectors:
      - type: method
        name: .*foo
```

Every class having a method that matches the regular expression `.*foo`, e.g. `getFoo()` or `setFoo()` becomes a part
of the *Foo services* layer.

### `implements` Collector

The `implements` collector allows collecting classes by matching recursively for a fully qualified interface name. 

```yaml
layers:
  - name: Foo
    collectors:
      - type: implements
        name: 'App\SomeInterface'
```

### More Collectors

As deptrac is in a very early state, feel free to contribute your own collector.


## Formatters

Deptrac has support for different output formatters with various options.

You can get a list of available formatters by running,

```bash
php deptrac.php analyze --help
```

*Hint*: Symfony Console does not allow to pass options to the default command. Therefore in order to use the formatter options you have to explicitly use the `analyze` command as shown above.

### Console Formatter

The default formatter is the console formatter, which dumps basic information to *STDOUT*,

```
examples\MyNamespace\Repository\SomeRepository::5 must not depend on examples\MyNamespace\Controllers\SomeController (Repository on Controller)
```

Supported options:

```
--formatter-console=         to disable the console fomatter, set this option to "false" [default: true]
```


### Graphviz Formatter

The Graphviz formatter is disabled by default. It could be activated with `--formatter-graphviz=true`.
Deptrac automatically tries to open the image generated by Graphviz.
You can disable automatic opening of the image by setting the `--formatter-graphviz-display=false` option, which is useful on CI-servers.

Supported options:

```
--formatter-graphviz=                   to activate the graphviz fomatter, set this option to "true" [default: false]
--formatter-graphviz-display=           should try to open graphviz image [default: true]
--formatter-graphviz-dump-image=        path to a dumped png file [default: ""]
--formatter-graphviz-dump-dot=          path to a dumped dot file [default: ""]
--formatter-graphviz-dump-html=         path to a dumped html file [default: ""]
```

*Hint*: You can create an image, a dot and an HTML file at the same time.


### JUnit Formatter

The JUnit formatter dumps a JUnit Report XML file, which is quite handy in CI environments.
It is disabled by default, to activate the formatter just use `--formatter-junit=true`.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<testsuites>
  <testsuite id="1" package="" name="Controller" timestamp="2018-06-07T10:09:34+00:00" hostname="localhost" tests="3" failures="2" errors="0" time="0">
    <testcase name="Controller-examples\Layer1\AnotherClassLikeAController" classname="examples\Layer1\AnotherClassLikeAController" time="0">
      <failure message="examples\Layer1\AnotherClassLikeAController:5 must not depend on examples\Layer2\SomeOtherClass (Controller on Layer2)" type="WARNING"/>
      <failure message="examples\Layer1\AnotherClassLikeAController:23 must not depend on examples\Layer2\SomeOtherClass (Controller on Layer2)" type="WARNING"/>
    </testcase>
  </testsuite>
  <testsuite id="2" package="" name="Layer2" timestamp="2018-06-07T10:09:34+00:00" hostname="localhost" tests="3" failures="4" errors="0" time="0">
    <testcase name="Layer2-examples\Layer2\SomeOtherClass2" classname="examples\Layer2\SomeOtherClass2" time="0">
      <failure message="examples\Layer2\SomeOtherClass2:5 must not depend on examples\Layer1\SomeClass2 (Layer2 on Layer1)" type="WARNING"/>
      <failure message="examples\Layer2\SomeOtherClass2:17 must not depend on examples\Layer1\SomeClass2 (Layer2 on Layer1)" type="WARNING"/>
    </testcase>
    <testcase name="Layer2-examples\Layer2\SomeOtherClass" classname="examples\Layer2\SomeOtherClass" time="0">
      <failure message="examples\Layer2\SomeOtherClass:5 must not depend on examples\Layer1\SomeClass (Layer2 on Layer1)" type="WARNING"/>
      <failure message="examples\Layer2\SomeOtherClass:17 must not depend on examples\Layer1\SomeClass (Layer2 on Layer1)" type="WARNING"/>
    </testcase>
  </testsuite>
</testsuites>
```

Supported options:

```
--formatter-junit=              to activate the JUnit fomatter, set this option to "true" [default: false]
--formatter-junit-dump-xml=     path to a dumped xml file [default: "./junit-report.xml"]
```


## Build Deptrac


To build deptrac, clone this repository and ensure you have the build dependencies installed:

- PHP in version 7.2 or above
- [Composer](https://getcomposer.org/)
- [Box](https://github.com/humbug/box)
- make

`cd` into your cloned directory, and call `make build`.

```bash
git clone https://github.com/sensiolabs-de/deptrac.git
cd deptrac
make build
```

This will create an executable file `deptrac.phar` in the current directory.
In order to use deptrac globally on your system, feel free to add it to your PATH (i.e. `/usr/local/bin`).


## Contribute

Deptrac is in a very early state, so it needs you to make it more awesome.

Feel free to report bugs, improve the documentation, request or even implement new features.
