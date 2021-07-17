<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap;

class FunctionReferenceBuilder extends ReferenceBuilder
{
    private string $functionName;

    /**
     * @param string[] $tokenTemplates
     */
    protected function __construct(array $tokenTemplates, string $filepath, string $functionName)
    {
        parent::__construct($tokenTemplates, $filepath);
        $this->functionName = $functionName;
    }

    /**
     * @param string[] $functionTemplates
     */
    public static function create(string $filepath, string $functionName, array $functionTemplates): self
    {
        return new self($functionTemplates, $filepath, $functionName);
    }

    /** @internal */
    public function build(): AstFunctionReference
    {
        return new AstFunctionReference(
            FunctionName::fromFQCN($this->functionName),
            $this->dependencies
        );
    }
}
