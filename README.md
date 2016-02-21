# deptrac

## What is Deptrac
Deptrac is a static code analysis tool that helps to enforce rules for dependencies between software layers.

### Cli Arguments

#### php deptrac.phar init
creates a dummy depfile in the current directory

#### php deptrac.phar
runs deptrac in the current directory

#### php deptrac.phar analyze [depfile]
runs deptrac from the current directory using the depfile [depfile]

### Installation
make sure that the `dot` command is available on your system.

```
brew install graphviz // osx + brew
sudo apt-get install graphviz // ubuntu
```

download the depfile.phar and run it using `php deptrac.phar`.


## Layers
Deptrac allows you to group different classes in "layers".
Technically layers are nothing more than collection of classes.
Every class can be in zero or more layers.

(Hopefully) most software is written with some kind of layers in mind.
For example a typically MVC application has at least controllers, models and views.

Deptrac allows you to visualize and enforce some kind of ruleset, based on such layer informations.

For example, by adopting MVC, most time you don't want your models to access controllers, but it's fine for controllers
to access models. Deptrac allows you to enforce and visualize such dependencies / rules.

### Collecting Layers
For example if your application has controllers and models, deptrac allows you to
group them in layers.

```yml
paths: ["./examples/ModelController"]
exclude_files: []
layers:
  - name: Models
    collectors:
      - type: className
        regex: .*MyNamespace\\Models\\.*
  - name: Controller
    collectors:
      - type: className
        regex: .*MyNamespace\\.*Controller.*
ruleset: []
```

In the first line we define (paths) the directory deptrac should analyze.
Using exclude_files we would be able to exclude some directories (by regex).

Things become more interesting in the layer part.
At first lets take a closer look at the first layer (with the name "Models").

We decided that our software has some kind of layer called "Models".
Every layer can have collectors.
Collectors are responsible for taking a closer look at your code and decide if a class is part of a layer.
For example by using the className collector you can define a regex for a classname.
Every classname (including namespace) that matches this regex is collected by the className collector and becomes a part of the layer.
In this example we define that everyClass that starts with MyNamespace\Models\ will be a part of the "Model" layer.

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
By default deptrac will raise a violation for every dependency between layers.
In real software you want to allow dependencies between different kind of layers.

For example a lot of architectures define some kind of *Controllers*, *Services* and *Repositories*.
A natural approach would be allowing:

- controllers to access service, but not repositories
- services to access repositories, but not controllers
- repositories neither services nor controllers.

We can define this using such a depfile:

```yml
paths: ["./examples/ControllerServiceRepository1/"]
exclude_files: []
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
  Repository:
```

Take a closer look t the rulset, here we whitelist that controller can access service and service can access repository.

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

it's totally fine to define multiple depfiles (views of the architecure) for different purposes.
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

