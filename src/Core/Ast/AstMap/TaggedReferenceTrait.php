<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap;

/**
 * Helper trait for implementing TaggedTokenReferenceInterface.
 *
 * Classes that use this trait must define $this->tags as array<string,list<string>>.
 *
 * @psalm-immutable
 */
trait TaggedReferenceTrait
{
    public function hasTag(string $name): bool
    {
        return isset($this->tags[$name]);
    }

    /**
     * @return ?list<string>
     */
    public function getTagLines(string $name): ?array
    {
        return $this->tags[$name] ?? null;
    }

    public function getTagText(string $name): ?string
    {
        $lines = $this->getTagLines($name);
        if ($lines) {
            return implode("\n", $lines);
        }

        return null;
    }
}
