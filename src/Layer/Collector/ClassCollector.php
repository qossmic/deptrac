<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Layer\Collector;

use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeType;

class ClassCollector extends AbstractTypeCollector
{
    protected function getType(): ClassLikeType
    {
        return ClassLikeType::class();
    }
}
