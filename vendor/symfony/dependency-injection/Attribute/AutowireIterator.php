<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\DependencyInjection\Attribute;

use DEPTRAC_202401\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
/**
 * Autowires an iterator of services based on a tag name.
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
class AutowireIterator extends Autowire
{
    /**
     * @param string|string[] $exclude A service or a list of services to exclude
     */
    public function __construct(string $tag, string $indexAttribute = null, string $defaultIndexMethod = null, string $defaultPriorityMethod = null, string|array $exclude = [], bool $excludeSelf = \true)
    {
        parent::__construct(new TaggedIteratorArgument($tag, $indexAttribute, $defaultIndexMethod, \false, $defaultPriorityMethod, (array) $exclude, $excludeSelf));
    }
}
