# Deptrac

Deptrac is a static code analysis tool for PHP that helps you communicate,
visualize and enforce architectural decisions in your projects. You can freely
define your architectural layers over classes and which rules should apply to
them.

For example, you can use Deptrac to ensure that bundles/modules/extensions in
your project are truly independent of each other to make them easier to reuse.

Deptrac can be used in a CI pipeline to make sure a pull request does not
violate any of the architectural rules you defined. With the optional Graphviz
formatter you can visualize your layers, rules and violations.

## Documentation

You can find the documentation in the /docs directory or visit the doc page:
https://qossmic.github.io/deptrac

## Getting Started

You will need to create a [configuration file](docs/depfile.md),
where you define your layers and communication ruleset. Once you have done that,
you can analyse your code by running deptrac:

```bash
php deptrac.phar

# which is equivalent to
php deptrac.phar analyse --config-file=deptrac.yaml
```

## Contribute

Deptrac is in active development. We are looking for your suggestions and help
to make it better.

Feel free to [open an issue](/issues) if you encounter bugs, have suggestions or
would like to add a new feature to Deptrac.

Please feel free to improve this documentation, fix bugs, or work on a suggested
feature by making a pull request on GitHub. Don't hesitate to ask for support,
if you need help at any point.

The [Contribution Guide](/docs/contributing.md) in the documentation contains
some advice for making a pull request with code changes.

### Code of Conduct

If you are **professional** and **polite** then everything will be alright.

Please don't be inconsiderate or mean, or anything in between.
