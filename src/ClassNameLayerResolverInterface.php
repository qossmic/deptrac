<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;

interface ClassNameLayerResolverInterface
{
    /**
     * @return string[]
     */
    public function getLayersByClassName(ClassLikeName $className): array;
}
