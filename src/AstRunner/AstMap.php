<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner;

use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;

class AstMap
{
    /**
     * @var AstClassReference[]
     */
    private $astClassReferences = [];

    /**
     * @var AstFileReference[]
     */
    private $astFileReferences = [];

    /**
     * @param AstFileReference[] $astFileReferences
     */
    public function __construct(array $astFileReferences)
    {
        foreach ($astFileReferences as $astFileReference) {
            $this->addAstFileReference($astFileReference);
        }
    }

    /**
     * @return AstClassReference[]
     */
    public function getAstClassReferences(): array
    {
        return $this->astClassReferences;
    }

    /**
     * @return AstFileReference[]
     */
    public function getAstFileReferences(): array
    {
        return $this->astFileReferences;
    }

    public function getClassReferenceByClassName(ClassLikeName $className): ?AstClassReference
    {
        return $this->astClassReferences[(string) $className] ?? null;
    }

    /**
     * @return AstInherit[]|iterable
     */
    public function getClassInherits(ClassLikeName $className): iterable
    {
        $classReference = $this->getClassReferenceByClassName($className);

        if (null === $classReference) {
            return [];
        }

        foreach ($classReference->getInherits() as $dep) {
            yield $dep;
            yield from $this->resolveDepsRecursive($dep);
        }
    }

    /**
     * @param \ArrayObject<string, bool>|null $alreadyResolved
     *
     * @return iterable<AstInherit>
     */
    private function resolveDepsRecursive(
        AstInherit $inheritDependency,
        \ArrayObject $alreadyResolved = null,
        \SplStack $path = null
    ): iterable {
        $alreadyResolved = $alreadyResolved ?? new \ArrayObject();

        if (null === $path) {
            $path = new \SplStack();
            $path->push($inheritDependency);
        }

        $className = (string) $inheritDependency->getClassName();

        if (isset($alreadyResolved[$className])) {
            $path->pop();

            return [];
        }

        $classReference = $this->getClassReferenceByClassName($inheritDependency->getClassName());

        if (null === $classReference) {
            return [];
        }

        foreach ($classReference->getInherits() as $inherit) {
            $alreadyResolved[$className] = true;

            yield $inherit->withPath(iterator_to_array($path));

            $path->push($inherit);

            yield from $this->resolveDepsRecursive($inherit, $alreadyResolved, $path);

            unset($alreadyResolved[$className]);
            $path->pop();
        }
    }

    private function addAstClassReference(AstClassReference $astClassReference): void
    {
        $this->astClassReferences[(string) $astClassReference->getClassName()] = $astClassReference;
    }

    private function addAstFileReference(AstFileReference $astFileReference): void
    {
        $this->astFileReferences[$astFileReference->getFilepath()] = $astFileReference;

        foreach ($astFileReference->getAstClassReferences() as $astClassReference) {
            $this->addAstClassReference($astClassReference);
        }
    }
}
