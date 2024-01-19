<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait PublicTrait
{
    /**
     * @return $this
     */
    public final function public() : static
    {
        $this->definition->setPublic(\true);
        return $this;
    }
    /**
     * @return $this
     */
    public final function private() : static
    {
        $this->definition->setPublic(\false);
        return $this;
    }
}
