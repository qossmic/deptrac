<?php

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\LayerResolver\ResolvedLayer;

interface ClassNameLayerResolverInterface
{
    /**
     * @param $className
     * @return ResolvedLayer[]
     */
    public function getLayersByClassName($className);
}
