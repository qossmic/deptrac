<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\RulesetEngine;

use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;

final class Warning
{
    /**
     * @var string
     */
    private $message;

    private function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @param string[] $layerNames
     */
    public static function classLikeIsInMoreThanOneLayer(
        ClassLikeName $getClassLikeNameA,
        array $layerNames
    ): self {
        return new self(sprintf(
            '%s is in more than one layer ["%s"]. It is recommended that one class should only be in one layer.',
            $getClassLikeNameA->toString(),
            implode('", "', $layerNames)
        ));
    }

    public function toString(): string
    {
        return $this->message;
    }
}
