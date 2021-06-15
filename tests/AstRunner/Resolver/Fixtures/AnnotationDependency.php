<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Integration\Fixtures;

final class AnnotationDependency
{
    /**
     * @var AnnotationDependencyChild
     */
    public $property;

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $parameter
     *
     * @return AnnotationDependencyChild[]
     *
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    public function test($parameter)
    {
        /** @var ?AnnotationDependencyChild $test */
        $test = null;

        /** @var AnnotationDependencyChild[] $children */
        $children = [];

        /** @var \Symfony\Component\Console\Exception\RuntimeException $fqn */
        $fqn = [];

        return [];
    }

    /**
     * @template T
     * @param T as mixed
     * @return AnnotationDependencyChild<T>
     */
    public function template($var)
    {
        return new AnnotationDependencyChild($var);
    }
}

/**
 * @template T
 */
final class AnnotationDependencyChild
{
    public function __construct($var)
    {
    }
}
