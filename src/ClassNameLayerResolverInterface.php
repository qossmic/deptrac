<?php

namespace SensioLabs\Deptrac;

interface ClassNameLayerResolverInterface
{
    /**
     * @param string $className
     * @return array
     */
    public function getLayersByClassName($className);
}
