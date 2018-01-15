<?php

namespace SensioLabs\Deptrac;

interface ClassNameLayerResolverInterface
{
    public function getLayersByClassName(string $className): array;
}
