<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Ast;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event fired before the ASTMap and parsing of all files has started.
 */
class PreCreateAstMapEvent extends Event
{
    public function __construct(
        public readonly int $expectedFileCount
    ) {
    }
}
