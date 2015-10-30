# Deptrac

## Todos
- Rename Collectors to LayerCollector
- Testcoverage
- Support for multiple depfiles

## What is Deptrac
Deptrac is a static code analysis tool that helps to manage dependencies in php code.
Most larger software is written in a layered style - or classes are grouped to some kind of groups like Symfony components.

Deptrac allows to group classes based on a configuration to "layers".
Dependencies between these layers can be visualized and enforced using a simple ruleset.

## Example

For this example we want to enforce that

## Installation

download the .phar (TODO URL).


## Running using HHVM
add this line to your php.ini hhvm.libxml.ext_entity_whitelist = file,http
run hhvm -c /usr/local/etc/php/5.6/php.ini deptrac.php
or for the phar version: hhvm -c /usr/local/etc/php/5.6/php.ini deptrac.phar