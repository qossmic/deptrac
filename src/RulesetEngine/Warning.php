<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\RulesetEngine;

use Qossmic\Deptrac\AstRunner\AstMap\TokenLikeName;

final class Warning
{
    private string $message;

    private function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @param string[] $layerNames
     */
    public static function tokenLikeIsInMoreThanOneLayer(
        TokenLikeName $tokenLikeName,
        array $layerNames
    ): self {
        return new self(sprintf(
                            '%s is in more than one layer ["%s"]. It is recommended that one token should only be in one layer.',
                            $tokenLikeName->toString(),
                            implode('", "', $layerNames)
        ));
    }

    public function toString(): string
    {
        return $this->message;
    }
}
