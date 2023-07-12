<?php

namespace Qossmic\Deptrac\Core\Layer\Collector;

use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Contract\Ast\TokenReferenceMetaDatumInterface;
use Qossmic\Deptrac\Contract\Layer\InvalidCollectorDefinitionException;
use Qossmic\Deptrac\Core\Ast\MetaData\PackageName;

class PackageNameCollector extends RegexCollector
{
    public function satisfy(array $config, TokenReferenceInterface $reference): bool
    {
        $regex = $this->getValidatedPattern($config);

        foreach ($this->getPackages($reference) as $package) {
            if (1 === preg_match($regex, $package)) {
                return true;
            }
        }

        return false;
    }

    protected function getPattern(array $config): string
    {
        if (!isset($config['value']) || !is_string($config['value'])) {
            throw new InvalidCollectorDefinitionException('PackageNameCollector needs the value configuration.');
        }

        return '/'.$config['value'].'/im';
    }

    /**
     * @return string[]
     */
    private function getPackages(TokenReferenceInterface $reference): array
    {
        $packageNameMetaData = array_filter(
            $reference->getMetaData(),
            fn (TokenReferenceMetaDatumInterface $metaData) => $metaData instanceof PackageName
        );
        return array_map(
            fn (PackageName $packageName) => $packageName->getPackageName(),
            $packageNameMetaData
        );
    }
}
