<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Contract\Dependency;

use DEPTRAC_202401\Symfony\Contracts\EventDispatcher\Event;
/**
 * Event triggered after all the dependencies have been resolved.
 */
final class PostEmitEvent extends Event
{
}
