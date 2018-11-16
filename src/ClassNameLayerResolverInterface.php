<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac;

interface ClassNameLayerResolverInterface
{
    /**
     * @return string[]
     */
    public function getLayersByClassName(string $className): array;

    /**
     * @return string[]
     */
    public function getLayers(): array;
}
