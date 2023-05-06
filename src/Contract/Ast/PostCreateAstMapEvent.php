<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Ast;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event fired after the ASTMap of all files has been created.
 */
class PostCreateAstMapEvent extends Event
{
}
