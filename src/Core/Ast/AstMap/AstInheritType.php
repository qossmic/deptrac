<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Ast\AstMap;

enum AstInheritType : string
{
    case EXTENDS = 'Extends';
    case IMPLEMENTS = 'Implements';
    case USES = 'Uses';
}
