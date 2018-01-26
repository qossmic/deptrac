<?php

namespace SensioLabs\Deptrac;

interface ClassNameLayerResolverInterface
{
    /**
     * @param string $className
     *
     * @return string[]
     */
    public function getLayersByClassName(string $className): array;
}
