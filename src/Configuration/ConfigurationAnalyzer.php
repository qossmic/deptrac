<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

use Qossmic\Deptrac\Configuration\Exception\InvalidConfigurationException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConfigurationAnalyzer
{
    /** @var array{count_use_statements: bool} */
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
            ->setDefault('count_use_statements', true)
            ->addAllowedTypes('count_use_statements', 'bool')
            ->resolve($args);

        return new self($options);
    }

    /**
     * @param array{count_use_statements: bool} $config
     */
    private function __construct(array $config)
    {
        $this->config = $config;
    }

    public function isCountingUseStatements(): bool
    {
        return $this->config['count_use_statements'];
    }
}
