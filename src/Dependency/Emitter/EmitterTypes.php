<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency\Emitter;

final class EmitterTypes
{
    public const CLASS_TOKEN = 'class';
    public const CLASS_SUPERGLOBAL_TOKEN = 'class_superglobal';
    public const FILE_TOKEN = 'file';
    public const FUNCTION_TOKEN = 'function';
    public const FUNCTION_SUPERGLOBAL_TOKEN = 'function_superglobal';
    public const USE_TOKEN = 'use';
}
