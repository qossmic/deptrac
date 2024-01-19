<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\DependencyInjection\Argument;

/**
 * Represents a collection of services found by tag name to lazily iterate over.
 *
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
class TaggedIteratorArgument extends IteratorArgument
{
    private string $tag;
    private mixed $indexAttribute;
    private ?string $defaultIndexMethod;
    private ?string $defaultPriorityMethod;
    private bool $needsIndexes;
    private array $exclude;
    private bool $excludeSelf = \true;
    /**
     * @param string      $tag                   The name of the tag identifying the target services
     * @param string|null $indexAttribute        The name of the attribute that defines the key referencing each service in the tagged collection
     * @param string|null $defaultIndexMethod    The static method that should be called to get each service's key when their tag doesn't define the previous attribute
     * @param bool        $needsIndexes          Whether indexes are required and should be generated when computing the map
     * @param string|null $defaultPriorityMethod The static method that should be called to get each service's priority when their tag doesn't define the "priority" attribute
     * @param array       $exclude               Services to exclude from the iterator
     * @param bool        $excludeSelf           Whether to automatically exclude the referencing service from the iterator
     */
    public function __construct(string $tag, string $indexAttribute = null, string $defaultIndexMethod = null, bool $needsIndexes = \false, string $defaultPriorityMethod = null, array $exclude = [], bool $excludeSelf = \true)
    {
        parent::__construct([]);
        if (null === $indexAttribute && $needsIndexes) {
            $indexAttribute = \preg_match('/[^.]++$/', $tag, $m) ? $m[0] : $tag;
        }
        $this->tag = $tag;
        $this->indexAttribute = $indexAttribute;
        $this->defaultIndexMethod = $defaultIndexMethod ?: ($indexAttribute ? 'getDefault' . \str_replace(' ', '', \ucwords(\preg_replace('/[^a-zA-Z0-9\\x7f-\\xff]++/', ' ', $indexAttribute))) . 'Name' : null);
        $this->needsIndexes = $needsIndexes;
        $this->defaultPriorityMethod = $defaultPriorityMethod ?: ($indexAttribute ? 'getDefault' . \str_replace(' ', '', \ucwords(\preg_replace('/[^a-zA-Z0-9\\x7f-\\xff]++/', ' ', $indexAttribute))) . 'Priority' : null);
        $this->exclude = $exclude;
        $this->excludeSelf = $excludeSelf;
    }
    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }
    public function getIndexAttribute() : ?string
    {
        return $this->indexAttribute;
    }
    public function getDefaultIndexMethod() : ?string
    {
        return $this->defaultIndexMethod;
    }
    public function needsIndexes() : bool
    {
        return $this->needsIndexes;
    }
    public function getDefaultPriorityMethod() : ?string
    {
        return $this->defaultPriorityMethod;
    }
    public function getExclude() : array
    {
        return $this->exclude;
    }
    public function excludeSelf() : bool
    {
        return $this->excludeSelf;
    }
}
