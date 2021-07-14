<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap\FunctionToken;

use Qossmic\Deptrac\AstRunner\AstMap\TokenReferenceBuilder;

class FunctionReferenceBuilder extends TokenReferenceBuilder
{
    private string $functionName;

    protected function __construct(string $filepath, string $functionName)
    {
        //TODO: Function templates (Patrick Kusebauch @ 10.07.21)
        parent::__construct([], $filepath);
        $this->functionName = $functionName;
    }

    public static function create(string $filepath, string $functionName): self
    {
        return new self($filepath, $functionName);
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
