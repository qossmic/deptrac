<?php

namespace SensioLabs\Deptrac\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OutputFormatterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $services = [];

        foreach ($container->findTaggedServiceIds('output_formatter') as $id => $exporterReference) {
            $services[] = $container->getDefinition($id);
        }

        $container->getDefinition('output_formatter_factory')->replaceArgument(0, $services);
    }
}
