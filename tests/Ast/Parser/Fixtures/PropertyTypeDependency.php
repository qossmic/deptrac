<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Integration\Fixtures;

use Symfony\Component\Finder\SplFileInfo;

final class PropertyTypeDependency
{
    public SplFileInfo $property;
    public ?\SplFileInfo $propertyNullable;
    public ?object $propertyObject; // should be ignored
    public string $propertyScalar; // should be ignored
    public $propertyNonTyped; // should be ignored

    public \DateTimeInterface|SplFileInfo $propertyUnion;
}
