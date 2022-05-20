--TEST--
Compare environment variables in GitHub Actions with local setup to investigate different output
--FILE--
<?php

var_dump($_SERVER['TERM']);
var_dump($_SERVER['COMMAND_MODE']);
var_dump($_SERVER['SHELL']);
var_dump($_SERVER['LC_CTYPE']);

--EXPECT--
string(14) "xterm-256color"
string(8) "unix2003"
string(8) "/bin/zsh"
string(11) "en_DE.UTF-8"
