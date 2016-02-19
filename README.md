# deptrac

## What is Deptrac
deptrac is a static code analysis tool that helps to enforce dependencies between software layers in php code.

## Example Configuration

```yml


```

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

![ModelController1][/sensiolabs-de/deptrac/blob/master/examples/ModelController1.png]








