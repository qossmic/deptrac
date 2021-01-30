<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;

interface ClassNameLayerResolverInterface
{
    /**
     * @return string[]
     */
    public function getLayersByClassName(ClassLikeName $className): array;
}
