<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Result;

/**
 * @psalm-immutable
 */
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

    public function toString(): string
    {
        return $this->message;
    }
}
