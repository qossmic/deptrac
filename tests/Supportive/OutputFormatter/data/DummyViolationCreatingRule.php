<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Supportive\OutputFormatter\data;

use Qossmic\Deptrac\Contract\Analyser\ViolationCreatingInterface;

class DummyViolationCreatingRule implements ViolationCreatingInterface
{
    public static function getSubscribedEvents()
    {
        return [];
    }

    public function ruleName(): string
    {
        return 'DummyRule';
    }

    public function ruleDescription(): string
    {
        return 'Why? Because!';
    }
}
