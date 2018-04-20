<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Dependency;

final class Events
{
    const PRE_EMIT = 'pre_dependency_emit';
    const POST_EMIT = 'post_dependency_emit';
    const PRE_FLATTEN = 'pre_dependency_flatten';
    const POST_FLATTEN = 'post_dependency_flatten';
}
