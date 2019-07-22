<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;

class NameScope
{
    /**
     * @var string|null
     */
    private $namespace;

    /**
     * @var string[] alias(string) => fullName(string)
     */
    private $uses;

    public function __construct(AstClassReference $classReference)
    {
        $this->namespace = $this->resolveNamespace($classReference->getClassName());
        $this->uses = $this->normalizeUses($classReference);
    }

    public function resolveStringName(string $name): string
    {
        if (0 === strpos($name, '\\')) {
            return ltrim($name, '\\');
        }

        $nameParts = explode('\\', $name);
        $firstNamePart = strtolower($nameParts[0]);

        if (isset($this->uses[$firstNamePart])) {
            if (1 === count($nameParts)) {
                return $this->uses[$firstNamePart];
            }
            array_shift($nameParts);

            return sprintf('%s\\%s', $this->uses[$firstNamePart], implode('\\', $nameParts));
        }

        if (null !== $this->namespace) {
            return sprintf('%s\\%s', $this->namespace, $name);
        }

        return $name;
    }

    private function resolveNamespace(string $className): ?string
    {
        $className = ltrim($className, '\\');
        $nameParts = explode('\\', $className);

        if (1 === count($nameParts)) {
            return null;
        }

        array_pop($nameParts);

        return implode('\\', $nameParts);
    }

    /**
     * @return string[] alias(string) => fullName(string)
     */
    private function normalizeUses(AstClassReference $astClassReference): array
    {
        if (null === $astClassReference->getFileReference()) {
            return [];
        }

        $uses = [];

        foreach ($astClassReference->getFileReference()->getDependencies() as $dependency) {
            if ('use' !== $dependency->getType()) {
                continue;
            }

            $className = $dependency->getClass();
            $nameParts = explode('\\', $className);
            $alias = end($nameParts);

            if (false === $alias) {
                $alias = $className;
            }

            $uses[$alias] = $className;
        }

        return $uses;
    }
}
