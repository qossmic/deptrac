<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Contract\Ast;

use DEPTRAC_202401\Symfony\Contracts\EventDispatcher\Event;
/**
 * Event triggered before the AST map and parsing of all files has started.
 */
final class PreCreateAstMapEvent extends Event
{
    public function __construct(public readonly int $expectedFileCount)
    {
    }
}
