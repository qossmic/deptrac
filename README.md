# Deptrac

## What is Deptrac
Deptrac is a static code analysis tool that helps to enforce rules for dependencies between software layers.

For example, you can define a rule like "controllers may not depend on models".
To ensure this, deptrac reads your code and find any usages of models in your controllers and will show you, where
this rule was violated. 

![ModelController1](examples/ControllerServiceRepository1.png)


## Getting Started

The easiert way to get started is to download the depfile.par.

At first, you need a depfile (written in YAML).
You can generate a bootstrapped `depfile.yml` with

```bash
php deptrac.phar init
```

In this file you define (mainly) three things:

1. The location of your sourcecode.
2. The layers of you application.
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


#### Quick Intro
In section `paths`, you declare, where deptrac should look for your code. As this is an array of directories, you can specify multiple locations.

With the `exclude_files` section, you can specify a regular expression for files, that should be excludes (like tests).

In the `layers` section we defined 3 Different Layers `Controller`, `Repository` and `Service` for example.
Deptrac is using collectors to group different Classes (in this case by the name of the class) together.

The `ruleset` section defines that every class of the "Controller"-layer may depend on classes that lives in the "Service"-layer,
and classes in the "Service"-layer may depend on classes in the "Repository"-layer.
Classes in the "Repository"-layer my not depend on any classes in different layers. This line could be omitted,
but this is more declarative.

If a class in the "Repository"-layer use a class in the "Service"-layer, deptrac wil recognize this and throw a violation for this case.
The same, if a "Service"-layer-class uses a "Controller"-layer-class.

## Run Deptrac

To execute deptrac, run

```bash
php deptrac.phar

# what es equivalent to
php deptrac.phar analyze depfile.yml
```


### Cli Arguments

##### php deptrac.phar init
creates a dummy depfile in the current directory

##### php deptrac.phar
runs deptrac in the current directory

##### php deptrac.phar analyze [depfile]
runs deptrac from the current directory using the depfile [depfile]

### Installation

### Graphviz
If you want to create graphical diagrams with your class dependencies, you will also need the `dot` command provided by [Graphviz](http://www.graphviz.org/).
There are packages for the usual package managers, for example:

```bash
brew install graphviz // osx + brew
sudo apt-get install graphviz // ubuntu
```

### Phar
download the depfile.phar and run it using `php deptrac.phar`.

### Build

For now, you have to build deptrac on your own.
To do this, you need the following software installed on your machine:

- PHP in version 5.5.9 or above
- [Composer](https://getcomposer.org/)
- [Box](http://box-project.github.io/box2/)
- make

Clone this repository, cd into it and run the make target:

```bash
git clone https://github.com/sensiolabs-de/deptrac.git && cd deptrac && make build
```

This will create a executable file `debtrac.phar` file in the current directory. Feel free to add it to your PATH (i.e. `/usr/local/bin/box`)


## Layers
Deptrac allows you to group different classes in "layers".
Technically layers are nothing more than collection of classes.

Each layer has a unique name and a list of collectors, that will look for classes, that should be assigned to this layer
(and yes, classes can be in more than one layer).

(Hopefully) most software is written with some kind of layers in mind.
For example a typically MVC application has at least controllers, models and views.

Deptrac allows you to visualize and enforce some kind of ruleset, based on such layer informations.

For example, you can define, that every class, that ends with `Controller` will be assigned to the "Controller"-layer, and
every class, that has a `\Model\` in its namespace will be added to the "Model"-layer.

For example, by adopting MVC, most time you don't want your models to access controllers, but it's fine for controllers
to access models. Deptrac allows you to enforce and visualize such dependencies / rules.


**Per default, any dependencies between layers are forbidden!**

### Collecting Layers
For example if your application has controllers and models, deptrac allows you to
group them in layers.

```yml
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

In the first line we define `paths` the directory deptrac should analyze.
Using `exclude_files` we would be able to exclude some directories by regex.

Things become more interesting in the `layers` part.
At first lets take a closer look at the first layer (with the name "Models").

We decided that our software has some kind of layer called "Models".
Every layer can have collectors.
Collectors are responsible for taking a closer look at your code and decide if a class is part of a layer.
For example by using the className collector you can define a regex for a class name.
Every class name (including namespace) that matches this regex is collected by the className collector and becomes a part of the layer.
In this example we define that every class that starts with MyNamespace\Models\ will be a part of the "Model" layer.

Every class that is in *\MyNamespace\* and contains the word controller will become a part of the "Controller" layer.

We can generate a dependency graph for the example configuration using:

```
php deptrac.php analyze examples/ModelController1.depfile.yml
```

Make sure that *graphviz* (dot) is installed on your system and you run php from your local system (for generating images).
You can install graphviz using:

```
brew install graphviz // osx + brew
sudo apt-get install graphviz // ubuntu
```

After deptrac finished the final png should be open:

![ModelController1](examples/ModelController1.png)

and deptrac will produce this output:

```
Start to create an AstMap for 2 Files.
Parsing File SomeController.php
Parsing File SomeModel.php
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

The output shows us that deptrac is parsing 2 files and found 0 violations.
By default every dependency between layers are violations.
In our case there are (for now) no dependencies between our classes (layers).
So it's fine that deptrac will show us 2 independent layers without any relationship.

## Violations
If we've 2 layers (Models, Controller) and one layer is using the other, deptrac will raise a violation by default.
for example a controller could try to use is the Model layer:

```php
// see the example in examples/ModelController2
namespace exmaples\MyNamespace\Controllers;

use exmaples\MyNamespace\Models\SomeModel;

class SomeController
{
    public function foo(SomeModel $m) {
        return $m;
    }
}

```

After running deptrac using:

```
php deptrac.php analyze examples/ModelController2.depfile.yml
```

we will get this output:

```
Start to create an AstMap for 2 Files.
Parsing File SomeController.php
Parsing File SomeModel.php
AstMap created.
start emitting dependencies "InheritanceDependencyEmitter"
start emitting dependencies "BasicDependencyEmitter"
end emitting dependencies
start flatten dependencies
end flatten dependencies
collecting violations.
formatting dependencies.
exmaples\MyNamespace\Controllers\SomeController::5 must not depend on exmaples\MyNamespace\Models\SomeModel (Controller on Models)
exmaples\MyNamespace\Controllers\SomeController::9 must not depend on exmaples\MyNamespace\Models\SomeModel (Controller on Models)

Found 2 Violations
```

![ModelController1](examples/ModelController2.png)

Deptrac is now finding 2 violations because the relation from the controller to models layer isn't allowed by default.
The console output shows exactly the lines deptrac found.

## Ruleset (Allowing Dependencies)

Allowed dependencies between layers are configured in rulesets.

By default deptrac will raise a violation for every dependency between layers.
In real software you want to allow dependencies between different kind of layers.

For example a lot of architectures define some kind of *Controllers*, *Services* and *Repositories*.
A natural approach would be allowing:

- controllers to access service, but not repositories
- services to access repositories, but not controllers
- repositories neither services nor controllers.

We can define this using such a depfile:

```yml
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

Take a closer look to the rulset, here we whitelist that controller can access service and service can access repository.

After running deptrac we'll get this result:

```
Start to create an AstMap for 3 Files.
Parsing File SomeController.php
Parsing File SomeRepository.php
Parsing File SomeService.php
AstMap created.
start emitting dependencies "InheritanceDependencyEmitter"
start emitting dependencies "BasicDependencyEmitter"
end emitting dependencies
start flatten dependencies
end flatten dependencies
collecting violations.
formatting dependencies.
exmaples\MyNamespace\Repository\SomeRepository::5 must not depend on exmaples\MyNamespace\Controllers\SomeController (Repository on Controller)
```

![ModelController1](examples/ControllerServiceRepository1.png)

Deptrac now finds a violation, if we take a closer look at the "SomeRepository" on line 5,
we'll see an unused use statement to a controller:

```php
namespace exmaples\MyNamespace\Repository;

use exmaples\MyNamespace\Controllers\SomeController;

class SomeRepository { }
```

Now we can remove the use statement and rerun deptrac - now without any violation.

## Different Layers And Different Views
In the example above we defined 3 different layers (controller, repository and service).
Deptrac gives architects the power to define what kind of layers exists.

Typically usecases are:

- caring about layers in different architectures (tier, hexagonal, ddd, ...)
- caring about dependencies between different kinds of services (infrastructure services / domain services / entities / dto's / ...)
- caring about coupling to third party code like composer vendors, frameworks, ...
- enforcing naming conventions
- ...

Typically software has more than just one view,
it's totally fine to use multiple depfiles, to take care about different architectural views.


## Collectors
Deptrac groups nodes in the ast to different layers.
Collectors decides if a node (class) is part of a layer.
From time to time deptrac will support more collectors out of the box and will provide an
easy way to extend deptrac with custom collectors.


### "className" Collector
Most examples are using the className collector.
The className collector allows collecting classes by the full qualified name (namespace + class).

example:

```yml
layers:
  - name: Controller
    collectors:
      - type: className
        regex: .*Controller.*

```

Every class (including namespace) that match the regex `.*Controller.*` becomes a part of the controller layer.

### "bool" Collector
The bool collector allows defining a collector based on other collectors.

```yml
layers:
  - name: Asset
    collectors:
      - type: bool
        must:
          - type: className
            regex: .*Foo\\Asset.*
          - type: className
            regex: .*Bar\\Asset.*
        must_not:
          - type: className
            regex: .*Assetic.*
```

The example shows an example of the bool collector.
Every class that contains (Foo\Asset OR Bar\Asset) and NOT Assetic will become a part of the asset layer.


## Formatters

Deptrac has support for different formatters with different options.

by running

```
php deptrac.php analyze --help
```

you can get a list of available formatters.

### Console Formatter
The console formatter is activated by default.
The Formatter dumps basic informations to stdout, example:

```
examples\MyNamespace\Repository\SomeRepository::5 must not depend on examples\MyNamespace\Controllers\SomeController (Repository on Controller)
```

Supported Arguments:

```
--formatter-console=         to disable the console fomatter, set this argument to 0 [default: 1]
```

### Graphviz Formatter
The graphviz formatter is activated by default.
After running deptrac by default the `--formatter-graphviz-display` is enabled, and deptrac tries to open the generated image.
For example on CI-Servers you can disable this using `--formatter-graphviz-display=0`.

```
--formatter-graphviz=                   to disable the graphviz fomatter, set this argument to 0 [default: 1]
--formatter-graphviz-display=           should try to open graphviz image [default: true]
--formatter-graphviz-dump-image=        path to a dumped png file [default: ""]
--formatter-graphviz-dump-dot=          path to a dumped dot file [default: ""]
--formatter-graphviz-dump-html=         path to a dumped html file [default: ""]
```

You can create an image, a dot file and a html file at the same time.