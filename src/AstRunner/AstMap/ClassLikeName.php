<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

final class ClassLikeName
{
    /**
     * @var string
     */
    private $className;

    private function __construct(string $className)
    {
        $this->className = ltrim($className, '\\');
    }

    public static function fromString(string $className): self
    {
        return new self($className);
    }

    public function match(string $pattern): bool
    {
        return 1 === preg_match($pattern, $this->className);
    }

    public function __toString()
    {
        return $this->className;
    }
}
