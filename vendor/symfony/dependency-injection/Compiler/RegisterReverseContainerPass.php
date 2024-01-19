<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\DependencyInjection\Compiler;

use DEPTRAC_202401\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\ContainerBuilder;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\ContainerInterface;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Definition;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Reference;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class RegisterReverseContainerPass implements CompilerPassInterface
{
    private bool $beforeRemoving;
    public function __construct(bool $beforeRemoving)
    {
        $this->beforeRemoving = $beforeRemoving;
    }
    /**
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('reverse_container')) {
            return;
        }
        $refType = $this->beforeRemoving ? ContainerInterface::IGNORE_ON_UNINITIALIZED_REFERENCE : ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE;
        $services = [];
        foreach ($container->findTaggedServiceIds('container.reversible') as $id => $tags) {
            $services[$id] = new Reference($id, $refType);
        }
        if ($this->beforeRemoving) {
            // prevent inlining of the reverse container
            $services['reverse_container'] = new Reference('reverse_container', $refType);
        }
        $locator = $container->getDefinition('reverse_container')->getArgument(1);
        if ($locator instanceof Reference) {
            $locator = $container->getDefinition((string) $locator);
        }
        if ($locator instanceof Definition) {
            foreach ($services as $id => $ref) {
                $services[$id] = new ServiceClosureArgument($ref);
            }
            $locator->replaceArgument(0, $services);
        } else {
            $locator->setValues($services);
        }
    }
}
