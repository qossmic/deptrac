<?php

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\OutputFormatter\GithubActionsOutputFormatter;

class GithubActionsOutputFormatterTest extends TestCase
{
    public function testGetName()
    {
        static::assertEquals('github-actions', (new GithubActionsOutputFormatter())->getName());
    }

    public function testFinish()
    {
        $this->markTestIncomplete('Not Implemented');
    }
}
