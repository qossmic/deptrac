<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Fixtures\UnassignedAnalyser;

use Exception;

class ClassBar
{
    public function test(): void
    {
        throw new Exception('');
    }

}
