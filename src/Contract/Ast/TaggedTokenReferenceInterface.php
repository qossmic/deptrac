<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Ast;

/**
 * Represents the AST-Token, its location, and associated tags.
 */
interface TaggedTokenReferenceInterface extends TokenReferenceInterface
{
    public function hasTag(string $name): bool;

    /**
     * @return array<string,string[]>|null
     */
    public function getTagLines(string $name): ?array;

    public function getTagText(string $name): ?string;
}
