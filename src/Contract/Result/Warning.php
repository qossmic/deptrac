<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Result;

use Stringable;

/**
 * @psalm-immutable
 */
final class Warning implements Stringable
{
    private function __construct(private readonly string $message)
    {
    }

    /**
     * @param string[] $layerNames
     */
    public static function tokenIsInMoreThanOneLayer(
        string $tokenName,
        array $layerNames
    ): self {
        return new self(sprintf(
            '%s is in more than one layer ["%s"]. It is recommended that one token should only be in one layer.',
            $tokenName,
            implode('", "', $layerNames)
        ));
    }

    public function __toString()
    {
        return $this->message;
    }
}
