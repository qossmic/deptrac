<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Ast\AstMap\FunctionLike;

use Qossmic\Deptrac\Ast\AstMap\ReferenceBuilder;

class FunctionLikeReferenceBuilder extends ReferenceBuilder
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
    public function build(): FunctionLikeReference
    {
        return new FunctionLikeReference(
            FunctionLikeToken::fromFQCN($this->functionName),
            $this->dependencies
        );
    }
}
