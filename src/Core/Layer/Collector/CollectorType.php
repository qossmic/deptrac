<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

enum CollectorType: string
{
    case TYPE_ATTRIBUTE = 'attribute';
    case TYPE_BOOL = 'bool';
    case TYPE_CLASS = 'class';
    case TYPE_CLASSLIKE = 'classLike';
    case TYPE_CLASS_NAME = 'className';
    case TYPE_CLASS_NAME_REGEX = 'classNameRegex';
    case TYPE_DIRECTORY = 'directory';
    case TYPE_EXTENDS = 'extends';
    case TYPE_FUNCTION_NAME = 'functionName';
    case TYPE_GLOB = 'glob';
    case TYPE_IMPLEMENTS = 'implements';
    case TYPE_INHERITANCE = 'inheritanceLevel';
    case TYPE_INHERITS = 'inherits';
    case TYPE_INTERFACE = 'interface';
    case TYPE_LAYER = 'layer';
    case TYPE_METHOD = 'method';
    case TYPE_SUPERGLOBAL = 'superglobal';
    case TYPE_TRAIT = 'trait';
    case TYPE_USES = 'uses';
}
