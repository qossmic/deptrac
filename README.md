# deptrac

## What is Deptrac
deptrac is a static code analysis tool that helps to enforce dependencies between software layers in php code.

### Cli Arguments
todo

### Installation
todo

## Layers
deptrac allows you to group different classes in "layers".
technically layers are nothing more than collection of classes.
classes can be in zero or more layers.

(hopefully) most software is written with some kind of layers in mind.
for example a typically MVC application has at least contollers, models and views.

deptrac allows you to visualize and enforce some kind of ruleset, based on such layer informations.

by adopting MVC most time you don't want your models to access controllers, but it's fine for controllers
to access models. deptrac allows you to enforce and visualize such dependencies / rules.

### Collecting Layers
for example if your application has controllers and models (MVC) deptrac allows you to
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

in first line we define (paths) the directory deptrac should analyze.
using exclude_files we would be able to exclude some directories (by regex).

things become more interesting in the layer part.
at first lets take a closer look at the first layer (with the name "Models").

we decided that our software has some kind of layer called "Models".
every layer can have collectors.
collectors are responsible for taking a closer look at your code and decide if a class is part of a layer.
for example by using the className collector you can define a regex for a classname.
every classname (including namespace) that matchs this regex is collected by the className collector and becomes a part of the layer.
in this example we define that everyClass that starts with MyNamespace\Models\ will be a part of the "Model" layer.

every class that is in *\MyNamespace\* and contains the word controller will become a part of the "Controller" layer.

we can generate a dependency graph for the example configuration using:

```
php deptrac.php analyze examples/ModelController1.depfile.yml
```

make sure that *graphviz* (dot) is installed on your system and you run php from your local system (for generating images).
you can install graphviz using:

```
brew install graphviz // osx + brew
sudo apt-get install graphviz // ubuntu
```

after deptrac finished the final png should be open:

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

the output shows us that deptrac is parsing 2 files and found 0 violations.
by default every dependency between layers are violations.
in out case there are (for now) no dependencies between our classes (layers).
so it's fine that deptrac will show us 2 independent layers without any relationship.

## Violations
if we've 2 layers (Models, Controller) and one layer is using the other, deptrac will raise a violation by default.
for example our controller is using the Model layer:

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

after running deptrac using:

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

deptrac is now finding 2 violations because the relation from the controller to models layer isn't defined as allowed.
the console output shows exacly the lines deptrac found.

## Ruleset (Allowing Dependencies)
by default deptrac will raise a violation for every dependency between layers.
in real software you want to allow dependencies between different kind of layers.

for example a lot of teams decide that they want to use *Controllers*, *Services* and *Repositories*.
a natural approach would be allowing:

- controllers to access service, but not repositories
- services to access repositories, but not controllers
- repositories neither services nor controllers.

we can define this using such a depfile:

```
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

take a closer look t the rulset, here we whitelist that controller can access service and service can access repository.

after running deptrac we'll get this result:

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

deptrac is finding a violation, if we take a closer look at the "SomeRepository" on line 5,
we'll see an unused use statement to a controller:

```php
namespace exmaples\MyNamespace\Repository;

use exmaples\MyNamespace\Controllers\SomeController;

class SomeRepository { }
```

now we can remove the use statement and rerun deptrac - now without any violation.

## Different Layers And Different Views
in the example above we defined 3 different layers (controller, repository and service).
deptrac gives architects the power to define what kind of layers exists.

it's totally fine to define multiple depfiles (views of the architecure) for different purposes.
typically usecases are:

- caring about layers in different architectures (tier, hexagonal, ddd, ...)
- caring about dependencies between different kinds of services (infrastructure services / domain services / entities / dto's / ...)
- caring about coupling to third party code like composer vendors, frameworks, ...
- enforcing naming conventions
- ...

typically software has more than just one view,
it's totally fine to use multiple depfiles to take care about different views (most time zoom levels) the architecure
of a software.


## Collectors
todo

### "className" Collector
todo

## Formatters
todo


### graphviz
todo

### console
todo
