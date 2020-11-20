#!/bin/sh

php deptrac.phar analyze examples/Uncovered.depfile.yaml --fail-on-uncovered --no-cache

if [ $? -ne 1 ]; then
  exit 1;
fi
