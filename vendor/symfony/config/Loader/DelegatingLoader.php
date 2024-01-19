<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\Config\Loader;

use DEPTRAC_202401\Symfony\Component\Config\Exception\LoaderLoadException;
/**
 * DelegatingLoader delegates loading to other loaders using a loader resolver.
 *
 * This loader acts as an array of LoaderInterface objects - each having
 * a chance to load a given resource (handled by the resolver)
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DelegatingLoader extends Loader
{
    public function __construct(LoaderResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }
    public function load(mixed $resource, string $type = null) : mixed
    {
        if (\false === ($loader = $this->resolver->resolve($resource, $type))) {
            throw new LoaderLoadException($resource, null, 0, null, $type);
        }
        return $loader->load($resource, $type);
    }
    public function supports(mixed $resource, string $type = null) : bool
    {
        return \false !== $this->resolver->resolve($resource, $type);
    }
}
