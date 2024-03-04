<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Contract\Analyser;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Config\AnalyserConfig;
use Qossmic\Deptrac\Contract\Config\DeptracConfig;
use Qossmic\Deptrac\Contract\Config\EmitterType;
use Qossmic\Deptrac\Supportive\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

final class DeptracConfigTest extends TestCase
{
    private function validateConfig(array $config)
    {
        $processor = new Processor();
        $def = new Configuration();

        $processor->process($def->getConfigTreeBuilder()->buildTree(), ['deptrac' => $config]);
        $this->addToAssertionCount(1);
    }

    public static function provideConfig()
    {
        $config = new DeptracConfig();
        $expected = [];
        yield 'empty' => [$config, $expected];

        $config = (new DeptracConfig())->analyser(AnalyserConfig::create()->types(
            EmitterType::FUNCTION_CALL
        ));
        $expected = [
            'analyser' => [
                'types' => [EmitterType::FUNCTION_CALL->value => EmitterType::FUNCTION_CALL->value],
            ],
        ];
        yield 'analyser types' => [$config, $expected];

        $config = (new DeptracConfig())->analyser(
            AnalyserConfig::create()->internalTag('@layer-internal')
        );
        $expected = [
            'analyser' => [
                'internal_tag' => '@layer-internal',
            ],
        ];
        yield 'internal_tag' => [$config, $expected];
    }

    /**
     * @dataProvider provideConfig
     */
    public function testConfigCompliance(DeptracConfig $config, array $expected): void
    {
        $array = $config->toArray();
        $this->validateConfig($array);
        $this->assertArrayContainsRecursive($expected, $array);
    }

    private function assertArrayContainsRecursive(array $expected, array $actual)
    {
        foreach ($expected as $key => $value) {
            self::assertArrayHasKey($key, $actual);

            if (is_array($value)) {
                $this->assertIsArray($actual[$key], $key);

                $this->assertArrayContainsRecursive($value, $actual[$key]);
            } else {
                $this->assertSame($value, $actual[$key], $key);
            }
        }
    }
}
