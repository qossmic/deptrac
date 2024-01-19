<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\DependencyInjection\ParameterBag;

use DEPTRAC_202401\Symfony\Component\DependencyInjection\Container;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ContainerBag extends FrozenParameterBag implements ContainerBagInterface
{
    private Container $container;
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    public function all() : array
    {
        return $this->container->getParameterBag()->all();
    }
    public function get(string $name) : array|bool|string|int|float|\UnitEnum|null
    {
        return $this->container->getParameter($name);
    }
    public function has(string $name) : bool
    {
        return $this->container->hasParameter($name);
    }
}
