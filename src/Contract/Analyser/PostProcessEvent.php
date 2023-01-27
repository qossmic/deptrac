<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Analyser;

use Symfony\Contracts\EventDispatcher\Event;

class PostProcessEvent extends Event
{
    public function __construct(private AnalysisResultBuilder $result)
    {
    }

    public function getResult(): AnalysisResultBuilder
    {
        return $this->result;
    }

    public function replaceResult(AnalysisResultBuilder $result): void
    {
        $this->result = $result;
    }
}
