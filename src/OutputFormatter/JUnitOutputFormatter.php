<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\RulesetEngine\Allowed;
use SensioLabs\Deptrac\RulesetEngine\Context;
use SensioLabs\Deptrac\RulesetEngine\Rule;
use SensioLabs\Deptrac\RulesetEngine\SkippedViolation;
use SensioLabs\Deptrac\RulesetEngine\Uncovered;
use SensioLabs\Deptrac\RulesetEngine\Violation;
use Symfony\Component\Console\Output\OutputInterface;

final class JUnitOutputFormatter implements OutputFormatterInterface
{
    private const DUMP_XML = 'dump-xml';

    public function getName(): string
    {
        return 'junit';
    }

    /**
     * @return OutputFormatterOption[]
     */
    public function configureOptions(): array
    {
        return [
            OutputFormatterOption::newValueOption(static::DUMP_XML, 'path to a dumped xml file', './junit-report.xml'),
        ];
    }

    public function enabledByDefault(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function finish(
        Context $context,
        OutputInterface $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $xml = $this->createXml($context);

        if ($dumpXmlPath = $outputFormatterInput->getOption(static::DUMP_XML)) {
            file_put_contents($dumpXmlPath, $xml);
            $output->writeln('<info>JUnit Report dumped to '.realpath($dumpXmlPath).'</info>');
        }
    }

    /**
     * @throws \Exception
     */
    private function createXml(Context $context): string
    {
        if (!class_exists(\DOMDocument::class)) {
            throw new \Exception('Unable to create xml file (php-xml needs to be installed)');
        }

        $xmlDoc = new \DOMDocument('1.0', 'UTF-8');
        $xmlDoc->formatOutput = true;

        $this->addTestSuites($context, $xmlDoc);

        return (string) $xmlDoc->saveXML();
    }

    private function addTestSuites(Context $context, \DOMDocument $xmlDoc): void
    {
        $testSuites = $xmlDoc->createElement('testsuites');

        $xmlDoc->appendChild($testSuites);

        $this->addTestSuite($context, $xmlDoc, $testSuites);
    }

    private function addTestSuite(Context $context, \DOMDocument $xmlDoc, \DOMElement $testSuites): void
    {
        $layers = [];
        foreach ($context->all() as $rule) {
            if ($rule instanceof Allowed || $rule instanceof Violation || $rule instanceof SkippedViolation) {
                $layers[$rule->getLayerA()][] = $rule;
            } elseif ($rule instanceof Uncovered) {
                $layers[$rule->getLayer()][] = $rule;
            }
        }

        $layerIndex = 0;
        foreach ($layers as $layer => $rules) {
            /** @var Violation[] $violationsByLayer */
            $violationsByLayer = array_filter($rules, static function (Rule $rule) {
                return $rule instanceof Violation;
            });

            /** @var SkippedViolation[] $skippedViolationsByLayer */
            $skippedViolationsByLayer = array_filter($rules, static function (Rule $rule) {
                return $rule instanceof SkippedViolation;
            });

            $rulesByClassName = [];
            foreach ($rules as $rule) {
                $rulesByClassName[$rule->getDependency()->getClassLikeNameA()->toString()][] = $rule;
            }

            $testSuite = $xmlDoc->createElement('testsuite');
            $testSuite->appendChild(new \DOMAttr('id', (string) ++$layerIndex));
            $testSuite->appendChild(new \DOMAttr('package', ''));
            $testSuite->appendChild(new \DOMAttr('name', $layer));
            $testSuite->appendChild(new \DOMAttr('hostname', 'localhost'));
            $testSuite->appendChild(new \DOMAttr('tests', (string) count($rulesByClassName)));
            $testSuite->appendChild(new \DOMAttr('failures', (string) count($violationsByLayer)));
            $testSuite->appendChild(new \DOMAttr('skipped', (string) count($skippedViolationsByLayer)));
            $testSuite->appendChild(new \DOMAttr('errors', '0'));
            $testSuite->appendChild(new \DOMAttr('time', '0'));

            $testSuites->appendChild($testSuite);

            $this->addTestCase($layer, $rulesByClassName, $xmlDoc, $testSuite);
        }
    }

    /**
     * @param array<string, Rule[]> $rulesByClassName
     */
    private function addTestCase(string $layer, array $rulesByClassName, \DOMDocument $xmlDoc, \DOMElement $testSuite): void
    {
        foreach ($rulesByClassName as $className => $rules) {
            $testCase = $xmlDoc->createElement('testcase');
            $testCase->appendChild(new \DOMAttr('name', $layer.' - '.$className));
            $testCase->appendChild(new \DOMAttr('classname', $className));
            $testCase->appendChild(new \DOMAttr('time', '0'));

            foreach ($rules as $rule) {
                if ($rule instanceof SkippedViolation) {
                    $this->addSkipped($xmlDoc, $testCase);
                } elseif ($rule instanceof Violation) {
                    $this->addFailure($rule, $xmlDoc, $testCase);
                } elseif ($rule instanceof Uncovered) {
                    $this->addWarning($rule, $xmlDoc, $testCase);
                }
            }

            $testSuite->appendChild($testCase);
        }
    }

    private function addFailure(Violation $violation, \DOMDocument $xmlDoc, \DOMElement $testCase): void
    {
        $dependency = $violation->getDependency();

        $message = sprintf(
            '%s:%d must not depend on %s (%s on %s)',
            $dependency->getClassLikeNameA()->toString(),
            $dependency->getFileOccurrence()->getLine(),
            $dependency->getClassLikeNameB()->toString(),
            $violation->getLayerA(),
            $violation->getLayerB()
        );

        $error = $xmlDoc->createElement('failure');
        $error->appendChild(new \DOMAttr('message', $message));
        $error->appendChild(new \DOMAttr('type', 'WARNING'));

        $testCase->appendChild($error);
    }

    private function addSkipped(\DOMDocument $xmlDoc, \DOMElement $testCase): void
    {
        $skipped = $xmlDoc->createElement('skipped');
        $testCase->appendChild($skipped);
    }

    private function addWarning(Uncovered $rule, \DOMDocument $xmlDoc, \DOMElement $testCase): void
    {
        $dependency = $rule->getDependency();

        $message = sprintf(
            '%s:%d has uncovered dependency on %s (%s)',
            $dependency->getClassLikeNameA()->toString(),
            $dependency->getFileOccurrence()->getLine(),
            $dependency->getClassLikeNameB()->toString(),
            $rule->getLayer()
        );

        $error = $xmlDoc->createElement('warning');
        $error->appendChild(new \DOMAttr('message', $message));
        $error->appendChild(new \DOMAttr('type', 'WARNING'));

        $testCase->appendChild($error);
    }
}
