<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeType;

class InterfaceCollector extends AbstractTypeCollector
{
    protected function getType(): ClassLikeType
    {
        return ClassLikeType::interface();
    }
}
