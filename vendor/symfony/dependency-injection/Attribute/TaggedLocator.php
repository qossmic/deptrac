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

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class TaggedLocator extends AutowireLocator
{
    public function __construct(public string $tag, public ?string $indexAttribute = null, public ?string $defaultIndexMethod = null, public ?string $defaultPriorityMethod = null, public string|array $exclude = [], public bool $excludeSelf = \true)
    {
        parent::__construct($tag, $indexAttribute, $defaultIndexMethod, $defaultPriorityMethod, $exclude, $excludeSelf);
    }
}
