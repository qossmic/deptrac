<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner\Resolver\Fixtures;

class AnnotationDependency
{
    /**
     * @var AnnotationDependencyChild
     */
    public $property;

    /**
     * @var array{a: AnnotationDependencyChild}
     */
    public $arrayShape;

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
}

class AnnotationDependencyChild
{
}
