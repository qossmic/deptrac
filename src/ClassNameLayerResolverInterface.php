<?php

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\Configuration\ConfigurationLayer;

interface ClassNameLayerResolverInterface
{
    /**
     * @param $className
     * @return ConfigurationLayer[]
     */
    public function getLayersByClassName($className);
}
