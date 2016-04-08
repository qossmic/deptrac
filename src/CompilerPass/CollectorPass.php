<?php

namespace SensioLabs\Deptrac\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CollectorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $services = [];

        foreach ($container->findTaggedServiceIds('collector') as $id => $exporterReference) {
            $services[] = $container->getDefinition($id);
        }

        $container->getDefinition('collector_factory')->replaceArgument(0, $services);
    }
}
