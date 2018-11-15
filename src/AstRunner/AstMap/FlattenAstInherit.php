<?php

namespace SensioLabs\Deptrac\AstRunner\AstMap;

class FlattenAstInherit implements AstInheritInterface
{
    private $path;
    private $inherit;

    /**
     * @param AstInheritInterface[] $path
     */
    public function __construct(AstInheritInterface $inherit, array $path)
    {
        $this->path = $path;
        $this->inherit = $inherit;
    }

    public function __toString(): string
    {
        $buffer = '';
        foreach ($this->path as $v) {
            $buffer = "{$v->__toString()} -> ".$buffer;
        }

        return "{$this->inherit->__toString()} (path: ".rtrim($buffer, ' -> ').')';
    }

    /**
     * @return AstInheritInterface[]
     */
    public function getPath(): array
    {
        return $this->path;
    }

    public function getClassName(): string
    {
        return $this->inherit->getClassName();
    }

    public function getLine(): int
    {
        return $this->inherit->getLine();
    }

    public function getType(): int
    {
        return $this->inherit->getType();
    }
}
