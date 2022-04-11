<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Event\RulesetEngine;

use Qossmic\Deptrac\RulesetEngine\Context;
use Symfony\Contracts\EventDispatcher\Event;

class PostRulesetProcessingEvent extends Event
{
    private Context $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function replaceContext(Context $context): void
    {
        $this->context = $context;
    }
}
