<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use DOMAttr;
use DOMDocument;
use DOMElement;
use Exception;
use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\RulesetEngine\Allowed;
use Qossmic\Deptrac\RulesetEngine\Context;
use Qossmic\Deptrac\RulesetEngine\Rule;
use Qossmic\Deptrac\RulesetEngine\SkippedViolation;
use Qossmic\Deptrac\RulesetEngine\Uncovered;
use Qossmic\Deptrac\RulesetEngine\Violation;

final class JUnitOutputFormatter implements OutputFormatterInterface
{
    public const DUMP_XML = 'junit-dump-xml';

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
            OutputFormatterOption::newValueOption(self::DUMP_XML, 'Path to a dumped xml file.', './junit-report.xml'),
        ];
    }

    public function enabledByDefault(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function finish(
        Context $context,
        Output $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $xml = $this->createXml($context);

        if ($dumpXmlPath = $outputFormatterInput->getOption(self::DUMP_XML)) {
            file_put_contents($dumpXmlPath, $xml);
            $output->writeLineFormatted('<info>JUnit Report dumped to '.realpath($dumpXmlPath).'</info>');
        }
    }

    /**
     * @throws Exception
     */
    private function createXml(Context $context): string
    {
        if (!class_exists(DOMDocument::class)) {
            throw new Exception('Unable to create xml file (php-xml needs to be installed)');
        }

        $xmlDoc = new DOMDocument('1.0', 'UTF-8');
        $xmlDoc->formatOutput = true;

        $this->addTestSuites($context, $xmlDoc);

        return (string) $xmlDoc->saveXML();
    }

    private function addTestSuites(Context $context, DOMDocument $xmlDoc): void
    {
        $testSuites = $xmlDoc->createElement('testsuites');

        $xmlDoc->appendChild($testSuites);

        if ($context->hasErrors()) {
            $testSuite = $xmlDoc->createElement('testsuite');
            $testSuite->appendChild(new DOMAttr('id', '0'));
            $testSuite->appendChild(new DOMAttr('package', ''));
            $testSuite->appendChild(new DOMAttr('name', 'Unmatched skipped violations'));
            $testSuite->appendChild(new DOMAttr('hostname', 'localhost'));
            $testSuite->appendChild(new DOMAttr('tests', '0'));
            $testSuite->appendChild(new DOMAttr('failures', '0'));
            $testSuite->appendChild(new DOMAttr('skipped', '0'));
            $testSuite->appendChild(new DOMAttr('errors', (string) count($context->errors())));
            $testSuite->appendChild(new DOMAttr('time', '0'));
            foreach ($context->errors() as $message) {
                $error = $xmlDoc->createElement('error');
                $error->appendChild(new DOMAttr('message', $message->toString()));
                $error->appendChild(new DOMAttr('type', 'WARNING'));
                $testSuite->appendChild($error);
            }

            $testSuites->appendChild($testSuite);
        }

        $this->addTestSuite($context, $xmlDoc, $testSuites);
    }

    private function addTestSuite(Context $context, DOMDocument $xmlDoc, DOMElement $testSuites): void
    {
        /** @var array<string, array<Rule>> $layers */
        $layers = [];
        foreach ($context->rules() as $rule) {
            if ($rule instanceof Allowed || $rule instanceof Violation || $rule instanceof SkippedViolation) {
                $layers[$rule->getLayerA()][] = $rule;
            } elseif ($rule instanceof Uncovered) {
                $layers[$rule->getLayer()][] = $rule;
            }
        }

        $layerIndex = 0;
        foreach ($layers as $layer => $rules) {
            $violationsByLayer = array_filter($rules, static function (Rule $rule) {
                return $rule instanceof Violation;
            });

            $skippedViolationsByLayer = array_filter($rules, static function (Rule $rule) {
                return $rule instanceof SkippedViolation;
            });

            $rulesByClassName = [];
            foreach ($rules as $rule) {
                $rulesByClassName[$rule->getDependency()->getTokenNameA()->toString()][] = $rule;
            }

            $testSuite = $xmlDoc->createElement('testsuite');
            $testSuite->appendChild(new DOMAttr('id', (string) ++$layerIndex));
            $testSuite->appendChild(new DOMAttr('package', ''));
            $testSuite->appendChild(new DOMAttr('name', $layer));
            $testSuite->appendChild(new DOMAttr('hostname', 'localhost'));
            $testSuite->appendChild(new DOMAttr('tests', (string) count($rulesByClassName)));
            $testSuite->appendChild(new DOMAttr('failures', (string) count($violationsByLayer)));
            $testSuite->appendChild(new DOMAttr('skipped', (string) count($skippedViolationsByLayer)));
            $testSuite->appendChild(new DOMAttr('errors', '0'));
            $testSuite->appendChild(new DOMAttr('time', '0'));

            $testSuites->appendChild($testSuite);

            $this->addTestCase($layer, $rulesByClassName, $xmlDoc, $testSuite);
        }
    }

    /**
     * @param array<string, Rule[]> $rulesByClassName
     */
    private function addTestCase(string $layer, array $rulesByClassName, DOMDocument $xmlDoc, DOMElement $testSuite): void
    {
        foreach ($rulesByClassName as $className => $rules) {
            $testCase = $xmlDoc->createElement('testcase');
            $testCase->appendChild(new DOMAttr('name', $layer.' - '.$className));
            $testCase->appendChild(new DOMAttr('classname', $className));
            $testCase->appendChild(new DOMAttr('time', '0'));

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

    private function addFailure(Violation $violation, DOMDocument $xmlDoc, DOMElement $testCase): void
    {
        $dependency = $violation->getDependency();

        $message = sprintf(
            '%s:%d must not depend on %s (%s on %s)',
            $dependency->getTokenNameA()->toString(),
            $dependency->getFileOccurrence()->getLine(),
            $dependency->getTokenNameB()->toString(),
            $violation->getLayerA(),
            $violation->getLayerB()
        );

        $error = $xmlDoc->createElement('failure');
        $error->appendChild(new DOMAttr('message', $message));
        $error->appendChild(new DOMAttr('type', 'WARNING'));

        $testCase->appendChild($error);
    }

    private function addSkipped(DOMDocument $xmlDoc, DOMElement $testCase): void
    {
        $skipped = $xmlDoc->createElement('skipped');
        $testCase->appendChild($skipped);
    }

    private function addWarning(Uncovered $rule, DOMDocument $xmlDoc, DOMElement $testCase): void
    {
        $dependency = $rule->getDependency();

        $message = sprintf(
            '%s:%d has uncovered dependency on %s (%s)',
            $dependency->getTokenNameA()->toString(),
            $dependency->getFileOccurrence()->getLine(),
            $dependency->getTokenNameB()->toString(),
            $rule->getLayer()
        );

        $error = $xmlDoc->createElement('warning');
        $error->appendChild(new DOMAttr('message', $message));
        $error->appendChild(new DOMAttr('type', 'WARNING'));

        $testCase->appendChild($error);
    }
}
