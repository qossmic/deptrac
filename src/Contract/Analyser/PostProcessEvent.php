<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Analyser;

use Qossmic\Deptrac\Contract\Result\Result;

class PostProcessEvent
{
    public function __construct(private Result $result)
    {
    }

    public function getResult(): Result
    {
        return $this->result;
    }

    public function replaceResult(Result $result): void
    {
        $this->result = $result;
    }
}
