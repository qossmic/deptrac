# Debugging

Deptrac provides a series of debug commands that help you identify issues in
your depfile.

* [`debug:layer`](#debuglayer)
* [`debug:class`](#debugclass)
* [`debug:unassigned`](#debugunassigned)

## `debug:layer`

With the `debug:layer`-command you can list all class-likes which are matched in
a specific layer.

```bash
php deptrac.phar debug:layer examples/DirectoryLayer.depfile.yaml Layer1

examples\Layer1\AnotherClassLikeAController
examples\Layer1\SomeClass
examples\Layer1\SomeClass2
```

## `debug:class`

The `debug:class`-command will let you know which layers a specified class-like
belongs to.

```bash
php deptrac.phar debug:class-like examples/DirectoryLayer.depfile.yaml 'examples\Layer1\AnotherClassLikeAController'

Controller
Layer1
```

## `debug:unassigned`

With the `debug:unassigned`-command you list all classes in your path that are
not assigned to any layer. This is useful to test that your collector
configuration for layers is correct.

```bash
php deptrac.phar debug:unassigned examples/DirectoryLayer.depfile.yaml

examples\Layer1\AnotherClassLikeAController
examples\Layer1\SomeClass
examples\Layer1\SomeClass2
```
