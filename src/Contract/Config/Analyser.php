<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Config;

final class Analyser
{
    /** @var array<string, EmitterType> */
    private array $types = [];

    /** @var ?string */
    private ?string $internalTag = null;

    private function __construct() {}

    /** @param ?array<array-key,EmitterType> $types */
    public static function create(array $types = null, string $internalTag = null): self
    {
        $analyser = new self();

        $types ??= [EmitterType::CLASS_TOKEN, EmitterType::FUNCTION_TOKEN];
        $analyser->types(...$types);

        if (null !== $internalTag) {
            $analyser->internalTag($internalTag);
        }

        return $analyser;
    }

    public function types(EmitterType ...$types): self
    {
        $this->types = [];
        foreach ($types as $type) {
            $this->types[$type->value] = $type;
        }

        return $this;
    }

    public function internalTag(?string $tag): self
    {
        $this->internalTag = $tag;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'types' => array_map(static fn (EmitterType $emitterType) => $emitterType->value, $this->types),
            'internal_tag' => $this->internalTag,
        ];
    }
}
