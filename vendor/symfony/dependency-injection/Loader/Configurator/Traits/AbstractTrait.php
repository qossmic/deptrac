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

trait AbstractTrait
{
    /**
     * Whether this definition is abstract, that means it merely serves as a
     * template for other definitions.
     *
     * @return $this
     */
    public final function abstract(bool $abstract = \true) : static
    {
        $this->definition->setAbstract($abstract);
        return $this;
    }
}
