<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\Config\Definition\Builder;

/**
 * An interface that can be implemented by nodes which build other nodes.
 *
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
interface BuilderAwareInterface
{
    /**
     * Sets a custom children builder.
     *
     * @return void
     */
    public function setBuilder(NodeBuilder $builder);
}
