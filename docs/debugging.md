# Debugging

Deptrac provides a series of debug commands that help you identify issues in
your depfile. All commands output one issue per line and can therefore be easily
combined with other tools like `wc` or `grep`.

* [`debug:layer`](#debuglayer)
* [`debug:token`](#debugtoken)
* [`debug:unassigned`](#debugunassigned)

## `debug:layer`

With the `debug:layer`-command you can list all tokens which are matched in
a specific layer.

```bash
php deptrac.phar debug:layer examples/DirectoryLayer.depfile.yaml Layer1

examples\Layer1\AnotherClassLikeAController
examples\Layer1\SomeClass
examples\Layer1\SomeClass2
```

## `debug:token`

The `debug:token` (previously `debug:class-like`)-command will let you know which layers a specified token belongs to.

```bash
php deptrac.phar debug:token examples/DirectoryLayer.depfile.yaml 'examples\Layer1\AnotherClassLikeAController' class-like

Controller
Layer1
```

## `debug:unassigned`

With the `debug:unassigned`-command you list all tokens in your path that are
not assigned to any layer. This is useful to test that your collector
configuration for layers is correct.

```bash
php deptrac.phar debug:unassigned examples/DirectoryLayer.depfile.yaml

examples\Layer1\AnotherClassLikeAController
examples\Layer1\SomeClass
examples\Layer1\SomeClass2
```
