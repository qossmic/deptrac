<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Analyser;

use Qossmic\Deptrac\Contract\Result\Result;
use Symfony\Contracts\EventDispatcher\Event;

class PostProcessEvent extends Event
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
