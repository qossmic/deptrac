<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

use Qossmic\Deptrac\Configuration\Exception\InvalidConfigurationException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConfigurationAnalyzer
{
    private const RECOGNIZED_TOKENS = [
        'class',
        'use',
        'function',
    ];
    
    /** @var array{types: string[]} */
    private array $config;

    /**
     * @param array<string, mixed> $args
     *
     * @throws InvalidConfigurationException
     */
    public static function fromArray(array $args): self
    {
        /** @var array{count_use_statements: bool} $options */
        $options = (new OptionsResolver())
            ->addAllowedTypes('count_use_statements', 'bool')
            ->addAllowedTypes('types', 'array')
            ->setDefault('count_use_statements', true)
            ->setDefault('types', ['class'])
            ->resolve($args);

        return new self($options);
    }

    /**
     * @param array{count_use_statements: bool, types: string[]} $config
     */
    private function __construct(array $config)
    {
        if($config['count_use_statements']) {
            $config['types'][] = 'use';
        }
        unset($config['count_use_statements']);

        foreach ($config['types'] as $type) {
            if(!in_array($type, self::RECOGNIZED_TOKENS, true)) {
                throw new \InvalidArgumentException('Unsupported analyzer type: ' . $type);
            }
        }
        $this->config = $config;
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        return $this->config['types'];
    }
}
