<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

final class ParameterResolver
{
    /**
     * @template T of array
     *
     * @param T                     $values
     * @param array<string, string> $parameters
     *
     * @return T
     */
    public function resolve(array $values, array $parameters): array
    {
        if ([] === $values || [] === $parameters) {
            return $values;
        }

        $keys = array_map(static fn (string $key): string => "%$key%", array_keys($parameters));

        return $this->replace($values, $keys, $parameters);
    }

    /**
     * @param array<string, string|array> $values
     * @param string[]                    $keys
     * @param array<string, string>       $parameters
     *
     * @return array<string, string|array>
     */
    private function replace(array $values, array $keys, array $parameters): array
    {
        foreach ($values as &$value) {
            if (is_array($value)) {
                /** @psalm-suppress MixedArgumentTypeCoercion */
                $value = $this->replace($value, $keys, $parameters);
            } else {
                $value = str_replace($keys, array_values($parameters), $value);
            }
        }

        return $values;
    }
}
