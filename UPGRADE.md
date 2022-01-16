# Upgrade from 0.19 to 0.20

## Depfile (Configuration File)

### What?

The previously deprecated parameter `analyser.count_use_statements` is removed.

### How?

Instead of enabling/disabling this option, set/remove the type in
`analyser.types` instead:

```yaml
parameters:
    analyser.types: ["class", "use"] # Default configuration
```

# Upgrade from 0.18 to 0.19

## Depfile (Configuration File)

### What?

* The location of the default configuration file has changed.

    ```
    From: ./depfile.yaml
    To:   ./deptrac.yaml
    ```

* The configuration inside the file must now be nested under `parameters:`
  except for imports. See docs for examples.

### How?

In your `depfile.yaml` you can just add a new section `parameters:` at the top
and then indent the remaining config 1 level under this section. The only
exception is `imports` which should stay on its current level and should not be
nested under parameters.

Example:
From: https://github.com/qossmic/deptrac/blob/0.18.0/depfile.yaml
To: https://github.com/qossmic/deptrac/blob/0.19.0/deptrac.yaml

After that, you can rename the file to `deptrac.yaml` to avoid the displayed
warning and ensure the file will automatically be loaded in future versions.
Alternatively you can keep your filename and make sure you load it via the new
`--config-file` option in the relevant commands instead of as an argument.

### Why?

In the future, we want to allow adding services to the configuration to extend
Deptrac's functionality, e.g. by providing custom collectors. That is why we
must separate the config into dedicated sections parameters and services.
