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

use DEPTRAC_202401\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
/**
 * Resolves all TaggedIteratorArgument arguments.
 *
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
class ResolveTaggedIteratorArgumentPass extends AbstractRecursivePass
{
    use PriorityTaggedServiceTrait;
    protected bool $skipScalars = \true;
    protected function processValue(mixed $value, bool $isRoot = \false) : mixed
    {
        if (!$value instanceof TaggedIteratorArgument) {
            return parent::processValue($value, $isRoot);
        }
        $exclude = $value->getExclude();
        if ($value->excludeSelf()) {
            $exclude[] = $this->currentId;
        }
        $value->setValues($this->findAndSortTaggedServices($value, $this->container, $exclude));
        return $value;
    }
}
