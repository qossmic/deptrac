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

trait ClassTrait
{
    /**
     * Sets the service class.
     *
     * @return $this
     */
    public final function class(?string $class) : static
    {
        $this->definition->setClass($class);
        return $this;
    }
}
