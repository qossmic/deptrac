--TEST--
Access environment variables
--ENV--
GITHUB_ACTIONS=true
--FILE--
<?php

require __DIR__ . '/../../vendor/autoload.php';

var_dump((new \SensioLabs\Deptrac\OutputFormatter\ConsoleOutputFormatter())->enabledByDefault());
var_dump((new \SensioLabs\Deptrac\OutputFormatter\GithubActionsOutputFormatter())->enabledByDefault());

--EXPECT--
bool(false)
bool(true)
