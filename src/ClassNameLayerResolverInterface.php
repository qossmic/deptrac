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

    /**
     * @return string[]
     */
    public function getLayers(): array;
}
