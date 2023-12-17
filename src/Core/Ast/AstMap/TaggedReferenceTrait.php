<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap;

/**
 * Helper trait for implementing TaggedTokenReferenceInterface.
 *
 * Classes that use this trait must define $this->tags as array<string,string[]>.
 */
trait TaggedReferenceTrait
{
    public function hasTag(string $name): bool
    {
        return isset($this->tags[$name]);
    }

    /**
     * @return string[]|null
     */
    public function getTagLines(string $name): ?array
    {
        return $this->tags[$name] ?? null;
    }

    public function getTagText(string $name): ?string
    {
        if ($this->hasTag($name)) {
            return implode("\n", $this->getTagLines($name));
        }

        return null;
    }
}
