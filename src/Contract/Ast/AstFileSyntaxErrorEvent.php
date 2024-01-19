<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Contract\Ast;

use DEPTRAC_202401\Symfony\Contracts\EventDispatcher\Event;
/**
 * Event triggered when parsing the AST failed on syntax error in the PHP file.
 */
final class AstFileSyntaxErrorEvent extends Event
{
    public function __construct(public readonly string $file, public readonly string $syntaxError)
    {
    }
}
