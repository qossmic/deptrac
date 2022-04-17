<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Layer\Collector;

final class CollectorTypes
{
    public const TYPE_BOOL = 'bool';
    public const TYPE_CLASS = 'class';
    public const TYPE_CLASSLIKE = 'classLike';
    public const TYPE_CLASS_NAME = 'className';
    public const TYPE_CLASS_NAME_REGEX = 'classNameRegex';
    public const TYPE_DIRECTORY = 'directory';
    public const TYPE_EXTENDS = 'extends';
    public const TYPE_FUNCTION_NAME = 'functionName';
    public const TYPE_IMPLEMENTS = 'implements';
    public const TYPE_INHERITANCE = 'inheritanceLevel';
    public const TYPE_INHERITS = 'inherits';
    public const TYPE_INTERFACE = 'interface';
    public const TYPE_LAYER = 'layer';
    public const TYPE_METHOD = 'method';
    public const TYPE_SUPERGLOBAL = 'superglobal';
    public const TYPE_TRAIT = 'trait';
    public const TYPE_USES = 'uses';
}
