<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\DependencyInjection\LazyProxy\Instantiator;

use DEPTRAC_202401\Symfony\Component\DependencyInjection\ContainerInterface;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Definition;
/**
 * Noop proxy instantiator - produces the real service instead of a proxy instance.
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RealServiceInstantiator implements InstantiatorInterface
{
    public function instantiateProxy(ContainerInterface $container, Definition $definition, string $id, callable $realInstantiator) : object
    {
        return $realInstantiator();
    }
}
