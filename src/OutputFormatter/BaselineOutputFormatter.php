<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\RulesetEngine\Context;
use Qossmic\Deptrac\RulesetEngine\SkippedViolation;
use Qossmic\Deptrac\RulesetEngine\Violation;
use Symfony\Component\Yaml\Yaml;

final class BaselineOutputFormatter implements OutputFormatterInterface
{
    private const DUMP_BASELINE = 'baseline-dump';

    public function getName(): string
    {
        return 'baseline';
    }

    public function configureOptions(): array
    {
        return [
            OutputFormatterOption::newValueOption(self::DUMP_BASELINE, 'path to a dumped baseline file', './depfile.baseline.yml'),
        ];
    }

    public function enabledByDefault(): bool
    {
        return false;
    }

    public function finish(
        Context $context,
        Output $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $groupedViolations = $this->collectViolations($context);

        if ($baselineFile = $outputFormatterInput->getOption(self::DUMP_BASELINE)) {
            file_put_contents(
                $baselineFile,
                Yaml::dump(
                    [
                            'skip_violations' => $groupedViolations,
                    ],
                    3,
                    2
                )
            );
            $output->writeLineFormatted('<info>Baseline dumped to '.realpath($baselineFile).'</info>');
        }
    }

    private function collectViolations(Context $context): array
    {
        $violations = [];
        foreach ($context->rules() as $rule) {
            if (!$rule instanceof Violation && !$rule instanceof SkippedViolation) {
                continue;
            }
            $dependency = $rule->getDependency();
            $dependantClass = $dependency->getDependant()->toString();
            $dependencyClass = $dependency->getDependee()->toString();

            if (!array_key_exists($dependantClass, $violations)) {
                $violations[$dependantClass] = [];
            }

            $violations[$dependantClass][$dependencyClass] = $dependencyClass;
        }

        return array_map(
            static function (array $dependencies): array {
                return array_values($dependencies);
            },
            $violations
        );
    }
}
