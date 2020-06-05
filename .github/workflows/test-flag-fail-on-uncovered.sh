#!/bin/sh

php deptrac.php analyze examples/Uncovered.depfile.yaml --fail-on-uncovered

if [ $? -ne 1 ]; then
  exit 1;
fi
