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
string(4) "true"
bool(false)
string(4) "test"
