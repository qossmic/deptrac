--TEST--
Access environment variables
--ENV--
GITHUB_ACTIONS=true
--FILE--
<?php

require __DIR__ . '/../../vendor/autoload.php';

var_dump(\Qossmic\Deptrac\Console\Command\AnalyseCommand::getDefaultFormatter());

--EXPECT--
string(14) "github-actions"