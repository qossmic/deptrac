<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Analyser;

use Symfony\Contracts\EventDispatcher\Event;

class PostProcessEvent extends Event
{
    public function __construct(private AnalysisResult $result)
    {
    }

    public function getResult(): AnalysisResult
    {
        return $this->result;
    }

    public function replaceResult(AnalysisResult $result): void
    {
        $this->result = $result;
    }
}
