<?php

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\OutputFormatter\GithubWorkflowOutputFormatter;

class GithubWorkflowOutputFormatterTest extends TestCase
{
    public function testGetName()
    {
        static::assertEquals('github-workflow', (new GithubWorkflowOutputFormatter())->getName());
    }

    public function testFinish()
    {
        $this->markTestIncomplete('Not Implemented');
    }
}
