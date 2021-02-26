<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

final class ParameterResolver
{
    /**
     * @param array<array-key, string|array> $values
     * @param array<string, string>          $parameters
     *
     * @return array<array-key, string|array>
     */
    public function resolve(array $values, array $parameters): array
    {
        if ([] === $values || [] === $parameters) {
            return $values;
        }

        $keys = array_map(static function (string $key) {
            return "%$key%";
        }, array_keys($parameters));

        $values = $this->replace($values, $keys, $parameters);

        return $values;
    }

    private function replace(array $values, array $keys, array $parameters): array
    {
        foreach ($values as &$value) {
            if (is_array($value)) {
                $value = $this->replace($value, $keys, $parameters);
            } else {
                $value = str_replace($keys, array_values($parameters), $value);
            }
        }

        return $values;
    }
}
