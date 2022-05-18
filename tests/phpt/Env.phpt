--TEST--
Access environment variables
--ENV--
FOO=true
TEST=test
--FILE--
<?php

require __DIR__ . '/../../vendor/autoload.php';

$env = new \Qossmic\Deptrac\Env();

var_dump($env->get('FOO'));
var_dump($env->get('BAR'));
var_dump($env->get('TEST'));

--EXPECT--
Standard input code:7:
string(4) "true"
Standard input code:8:
bool(false)
Standard input code:9:
string(4) "test"
