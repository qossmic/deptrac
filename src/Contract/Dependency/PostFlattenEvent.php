<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Dependency;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event fired after all the dependencies have been flattened.
 *
 * This occurs when all dependencies caused by class inheritance have been resolved.
 */
final class PostFlattenEvent extends Event
{
}
